<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\IssueId;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasRejected extends AggregateChanged
{
    public static function bySystem(IssueId $id, string $externalServiceId): self
    {
        return self::occur((string) $id, ['externalServiceId' => $externalServiceId]);
    }

    public function externalServiceId(): string
    {
        return $this->payload()['externalServiceId'];
    }
}
