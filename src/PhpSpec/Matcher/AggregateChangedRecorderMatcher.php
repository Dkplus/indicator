<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Matcher\BasicMatcher;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Prooph\EventStore\EventStore;
use ReflectionFunction;

class AggregateChangedRecorderMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return $name === 'haveRecorded'
            && count($arguments) === 1
            || (count($arguments) === 2
                && is_string($arguments[0])
                && is_callable($arguments[1]));
    }

    protected function matches($subject, array $arguments)
    {
        $recordedEvents = $this->extractEvents($subject);
        $expectedEvent = $arguments[0];

        if ($expectedEvent instanceof AggregateChanged) {
            return $this->testFromEventObject($recordedEvents, $expectedEvent);
        }

        if (is_string($expectedEvent)) {
            return $this->testFromEventClass($recordedEvents, $expectedEvent);
        }

        if (is_callable($arguments[0])) {
            return $this->testFromCallable($recordedEvents, $expectedEvent);
        }

        return false;
    }

    /**
     * @param AggregateChanged[] $recordedEvents
     * @param AggregateChanged $expectedEvent
     * @return bool
     */
    private function testFromEventObject($recordedEvents, AggregateChanged $expectedEvent): bool
    {
        foreach ($recordedEvents as $recordedEvent) {
            if ($recordedEvent instanceof $expectedEvent
                && $expectedEvent->aggregateId() === $recordedEvent->aggregateId()
                && $expectedEvent->payload() === $recordedEvent->payload()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AggregateChanged[] $recordedEvents
     * @param string $expectedEventClass
     * @return bool
     */
    private function testFromEventClass($recordedEvents, string $expectedEventClass): bool
    {
        foreach ($recordedEvents as $recordedEvent) {
            if ($recordedEvent instanceof $expectedEventClass) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param AggregateChanged[] $recordedEvents
     * @param callable $callable
     * @return bool
     */
    private function testFromCallable($recordedEvents, callable $callable): bool
    {
        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();

        $test = $callable;
        if (count($parameters) > 0 && $parameters[0]->hasType() && ! $parameters[0]->getType()->isBuiltin()) {
            $expectedEventClass = (string) $parameters[0]->getType();
            $test = function ($event) use ($callable, $expectedEventClass) {
                return $event instanceof $expectedEventClass && $callable($event);
            };
        }

        foreach ($recordedEvents as $recordedEvent) {
            if ($test($recordedEvent)) {
                return true;
            }
        }
        return false;
    }

    protected function extractEvents($subject)
    {
        if ($subject instanceof AggregateRoot) {
            return AggregateRootDecorator::newInstance()->extractRecordedEvents($subject);
        }
        if ($subject instanceof EventStore) {
            return $subject->getRecordedEvents();
        }
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        if ($subject instanceof EventStore) {
            return new FailureException(sprintf(
                'Expected to have %s recorded, but does not.',
                self::eventToString($arguments[0])
            ));
        }
        return new FailureException(sprintf(
            'Expected %s to have %s recorded, but it does not.',
            self::aggregateToString($subject),
            self::eventToString($arguments[0])
        ));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        if ($subject instanceof EventStore) {
            return new FailureException(sprintf(
                'Expected not to have %s recorded, but does.',
                self::eventToString($arguments[0])
            ));
        }
        return new FailureException(sprintf(
            'Expected %s not to have %s recorded, but it does.',
            self::aggregateToString($subject),
            self::eventToString($arguments[0])
        ));
    }

    private static function aggregateToString($aggregate): string
    {
        return sprintf(
            '[%s:%s]',
            get_class($aggregate),
            (string) AggregateRootDecorator::newInstance()->extractAggregateId($aggregate)
        );
    }

    private static function eventToString($eventOrClassName): string
    {
        return $eventOrClassName instanceof DomainEvent
            ? '[' . get_class($eventOrClassName) . ':' . json_encode($eventOrClassName->payload()) . ']'
            : (string) $eventOrClassName;
    }
}
