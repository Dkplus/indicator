<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Assert\Assertion;

class IssueType
{
    /** @var string */
    private $type;

    public static function enhancement(): self
    {
        return new self('enhancement');
    }

    public static function bugReport(): self
    {
        return new self('bug');
    }

    public static function question(): self
    {
        return new self('question');
    }

    public static function fromString(string $type)
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        Assertion::inArray($type, ['enhancement', 'bug', 'question']);
        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
