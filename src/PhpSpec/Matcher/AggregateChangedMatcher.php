<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use PhpSpec\Matcher\Matcher;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;
use Prooph\EventStore\EventStore;
use RuntimeException;

class AggregateChangedMatcher implements Matcher
{
    /** @var EventFilter */
    private $filter;

    /** @var EventMatcher[] */
    private $matchers = [];

    public function __construct()
    {
        $this->filter = new EventFilter();
        $this->matchers = [
            new HaveRecordedMatcher(),
            new HaveRecordedOnceMatcher(),
        ];
    }

    public function supports($name, $subject, array $arguments)
    {
        if (! $subject instanceof AggregateRoot
            && ! $subject instanceof EventStore
        ) {
            return false;
        }

        return $this->matcherFor($name, $arguments) instanceof EventMatcher;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        $matcher = $this->matcherFor($name, $arguments);
        $filteredEvents = $this->extractAndFilterEvents($subject, $arguments, $matcher);

        if (false === $matcher->matches($filteredEvents, $arguments)) {
            $matcher->throwFailure($arguments, $filteredEvents, $subject instanceof AggregateRoot ? $subject : null);
        }

        return $subject;
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        $matcher = $this->matcherFor($name, $arguments);
        $filteredEvents = $this->extractAndFilterEvents($subject, $arguments, $matcher);

        if (true === $matcher->matches($filteredEvents, $arguments)) {
            $matcher->throwNegativeFailure($arguments, $subject instanceof AggregateRoot ? $subject : null);
        }

        return $subject;
    }

    public function getPriority()
    {
        return 100;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return EventMatcher|null
     */
    private function matcherFor(string $name, array $arguments)
    {
        foreach ($this->matchers as $each) {
            if ($each->supports($name, $arguments)) {
                return $each;
            }
        }
        return null;
    }

    private function extractAndFilterEvents($subject, array $arguments, EventMatcher $matcher): array
    {
        $recordedEvents = $this->extractEvents($subject);
        return $matcher->filter($recordedEvents, $arguments, $this->filter);
    }

    protected function extractEvents($subject)
    {
        if ($subject instanceof AggregateRoot) {
            return AggregateRootDecorator::newInstance()->extractRecordedEvents($subject);
        }
        if ($subject instanceof EventStore) {
            return $subject->getRecordedEvents();
        }
        throw new RuntimeException('This should be never reached.');
    }
}
