<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Ramsey\Uuid\Uuid;

class CustomerId
{
    /** @var string */
    private $uuid;

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    private function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function equals($anotherId)
    {
        return $this->uuid === (string) $anotherId;
    }
}
