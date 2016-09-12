<?php
declare(strict_types=1);
namespace Dkplus\Indicator\PhpSpec\Matcher;

use Prooph\EventSourcing\AggregateRoot;

interface EventMatcher
{
    public function supports(string $name, array $arguments): bool;

    public function filter(array $recordedEvents, array $arguments, EventFilter $filter): array;

    public function matches(array $filteredEvents, array $arguments): bool;

    public function throwFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null);

    public function throwNegativeFailure(array $arguments, array $filteredEvents, AggregateRoot $subject = null);
}
