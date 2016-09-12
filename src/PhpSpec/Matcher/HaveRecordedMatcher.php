<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use Prooph\EventSourcing\AggregateRoot;

class HaveRecordedMatcher extends BasicEventMatcher
{
    public function supports(string $name, array $arguments): bool
    {
        return $name === 'haveRecorded'
            && parent::supports($name, $arguments);
    }

    public function matches(array $filteredEvents, array $arguments): bool
    {
        return count($filteredEvents) >= 1;
    }

    public function throwFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null)
    {
        throw Failure::expected($subject, 'to have %s recorded, but it does not.', $arguments[0]);
    }

    public function throwNegativeFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null)
    {
        throw Failure::expected($subject, 'not to have %s recorded, but it does.', $arguments[0]);
    }
}
