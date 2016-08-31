<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\CustomerId;
use Prooph\EventSourcing\AggregateChanged;

class CustomerWasRegistered extends AggregateChanged
{
    public static function withIdAndName(CustomerId $id, string $name)
    {
        return self::occur((string) $id, ['name' => $name]);
    }

    public function name(): string
    {
        return $this->payload()['name'];
    }
}
