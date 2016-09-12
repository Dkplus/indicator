<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use Prooph\EventSourcing\AggregateRoot;

class HaveRecordedOnceMatcher extends BasicEventMatcher
{
    public function supports(string $name, array $arguments): bool
    {
        return $name === 'haveRecordedOnce'
        && parent::supports($name, $arguments);
    }

    public function matches(array $filteredEvents, array $arguments): bool
    {
        return count($filteredEvents) === 1;
    }

    public function throwFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null)
    {
        throw Failure::expected(
            $subject,
            'to have %s recorded once, but it does record it %sx.',
            $arguments[0],
            count($filteredEvents)
        );
    }

    public function throwNegativeFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null)
    {
        throw Failure::expected($subject, 'not to have %s recorded once, but it does record it once.', $arguments[0]);
    }
}
