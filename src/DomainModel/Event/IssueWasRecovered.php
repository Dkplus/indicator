<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use DateTimeImmutable;
use DateTimeZone;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueState;
use Dkplus\Indicator\DomainModel\IssueType;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasRecovered extends AggregateChanged
{
    public static function fromExternalService(
        IssueId $id,
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        IssueType $type,
        DateTimeImmutable $createdAt,
        CustomerId $reporterId = null
    ): self {
        return self::occur((string) $id, [
            'reporterId' => (string) $reporterId,
            'title' => $title,
            'text' => $text,
            'issueNumber' => $issueNumber,
            'externalServiceId' => $externalServiceId,
            'state' => (string) $state,
            'type' => (string) $type,
            'originallyCreatedAt' => $createdAt->getTimestamp(),
        ]);
    }

    public function reporterId(): string
    {
        return $this->payload()['reporterId'];
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

    public function open(): bool
    {
        return IssueState::fromString($this->state())->isOpen();
    }

    public function type(): string
    {
        return $this->payload()['type'];
    }

    public function originallyCreatedAt(): DateTimeImmutable
    {
        return (new DateTimeImmutable('@' . $this->payload()['originallyCreatedAt']))
            ->setTimezone(new DateTimeZone('UTC'));
    }
}
