<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueId;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasWithdrawn extends AggregateChanged
{
    public static function byUser(IssueId $id, CustomerId $customerId, string $externalServiceId = null)
    {
        return self::occur(
            (string) $id,
            ['customerId' => (string) $customerId, 'externalServiceId' => $externalServiceId]
        );
    }

    public function customerId(): string
    {
        return $this->payload()['customerId'];
    }

    /** @return string|null */
    public function externalServiceId()
    {
        return $this->payload()['externalServiceId'];
    }
}
