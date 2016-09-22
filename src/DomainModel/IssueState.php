<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Assert\Assertion;

class IssueState
{
    /** @var string */
    private $state;

    public static function opened(): self
    {
        return new IssueState('open');
    }

    public static function implemented(): self
    {
        return new IssueState('implemented');
    }

    public static function rejected(): self
    {
        return new IssueState('rejected');
    }

    public static function closed(): self
    {
        return new IssueState('closed');
    }

    public static function fromString($state): self
    {
        return new IssueState($state);
    }

    private function __construct(string $state)
    {
        Assertion::inArray($state, ['open', 'implemented', 'rejected', 'closed']);
        $this->state = $state;
    }

    public function isOpen(): bool
    {
        return $this->state === 'open';
    }

    public function equals(self $anotherState): bool
    {
        return $this->state === $anotherState->state;
    }

    public function __toString(): string
    {
        return $this->state;
    }
}
