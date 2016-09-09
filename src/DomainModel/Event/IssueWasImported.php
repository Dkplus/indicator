<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueState;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasImported extends AggregateChanged
{
    public static function fromExternalService(
        IssueId $id,
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        CustomerId $customerId = null
    ): self {
        return self::occur((string) $id, [
            'title' => $title,
            'text' => $text,
            'issueNumber' => $issueNumber,
            'externalServiceId' => $externalServiceId,
            'state' => (string) $state,
            'customerId' => isset($customerId) ? (string) $customerId : null,
        ]);
    }

    public function title(): string
    {
        return $this->payload()['title'];
    }

    public function text(): string
    {
        return $this->payload()['text'];
    }

    public function issueNumber(): string
    {
        return $this->payload()['issueNumber'];
    }

    public function externalServiceId(): string
    {
        return $this->payload()['externalServiceId'];
    }

    public function state(): string
    {
        return $this->payload()['state'];
    }

    /** @return string|null */
    public function customerId()
    {
        return $this->payload()['customerId'];
    }
}
