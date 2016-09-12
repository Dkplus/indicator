<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use Prooph\EventSourcing\AggregateChanged;

abstract class BasicEventMatcher implements EventMatcher
{
    public function supports(string $name, array $arguments): bool
    {
        return isset($arguments[0])
            && ($arguments[0] instanceof AggregateChanged
                || is_string($arguments[0])
                || is_callable($arguments[0]));
    }

    public function filter(array $recordedEvents, array $arguments, EventFilter $filter): array
    {
        $expectedEvent = $arguments[0];

        if ($expectedEvent instanceof AggregateChanged) {
            return $filter->byEventObject($recordedEvents, $expectedEvent);
        }

        if (is_string($expectedEvent)) {
            return $filter->byEventClass($recordedEvents, $expectedEvent);
        }

        if (is_callable($arguments[0])) {
            return $filter->byCallable($recordedEvents, $expectedEvent);
        }

        return [];
    }
}
