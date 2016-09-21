<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Ramsey\Uuid\Uuid;

class CustomerId
{
    /** @var string */
    private $id;

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals($anotherId)
    {
        return $this->id === (string) $anotherId;
    }
}
