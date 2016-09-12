<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use Prooph\EventSourcing\AggregateChanged;
use ReflectionFunction;
use Traversable;

class EventFilter
{
    /**
     * @param AggregateChanged[] $recordedEvents
     * @param AggregateChanged $expectedEvent
     * @return AggregateChanged[]
     */
    public function byEventObject($recordedEvents, AggregateChanged $expectedEvent): array
    {
        return $this->filter(
            $recordedEvents,
            function (AggregateChanged $recordedEvent) use ($expectedEvent) {
                return $recordedEvent instanceof $expectedEvent
                    && $recordedEvent->aggregateId() === $expectedEvent->aggregateId()
                    && $expectedEvent->payload() === $recordedEvent->payload();
            }
        );
    }

    /**
     * @param AggregateChanged[] $recordedEvents
     * @param string $expectedEventClass
     * @return AggregateChanged[]
     */
    public function byEventClass($recordedEvents, string $expectedEventClass): array
    {
        return $this->filter(
            $recordedEvents,
            function (AggregateChanged $recordedEvent) use ($expectedEventClass) {
                return $recordedEvent instanceof $expectedEventClass;
            }
        );
    }

    /**
     * @param AggregateChanged[] $recordedEvents
     * @param $callable $callable
     * @return AggregateChanged[]
     */
    public function byCallable($recordedEvents, callable $callable): array
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

        return $this->filter($recordedEvents, $test);
    }

    private function filter($recordedEvents, callable $filter): array
    {
        if ($recordedEvents instanceof Traversable) {
            $recordedEvents = iterator_to_array($recordedEvents);
        }
        return array_filter($recordedEvents, $filter);
    }
}
