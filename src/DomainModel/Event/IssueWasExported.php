<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\IssueId;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasExported extends AggregateChanged
{
    public static function toExternalService(
        IssueId $id,
        string $ticketNumber,
        string $externalServiceId
    ) {
        return self::occur(
            (string) $id,
            ['issueNumber' => $ticketNumber, 'externalServiceId' => $externalServiceId]
        );
    }

    public function issueNumber(): string
    {
        return $this->payload()['issueNumber'];
    }

    public function externalServiceId(): string
    {
        return $this->payload()['externalServiceId'];
    }
}
