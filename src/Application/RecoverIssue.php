<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use DateTimeImmutable;
use DateTimeZone;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\UuidInterface;

class RecoverIssue extends Command  implements PayloadConstructable
{
    use PayloadTrait;

    public static function fromExternalService(
        UuidInterface $issueId,
        string $title,
        string $text,
        string $state,
        string $type,
        string $issueNumber,
        string $externalServiceId,
        string $customerId = null,
        DateTimeImmutable $originallyCreatedAt
    ): self {
        return new self([
            'issueId' => $issueId->toString(),
            'title' => $title,
            'text' => $text,
            'state' => $state,
            'type' => $type,
            'issueNumber' => $issueNumber,
            'externalServiceId' => $externalServiceId,
            'reporterId' => $customerId,
            'originallyCreatedAt' => $originallyCreatedAt->getTimestamp(),
        ]);
    }

    public function issueId(): string
    {
        return $this->payload()['issueId'];
    }

    public function title(): string
    {
        return $this->payload()['title'];
    }

    public function text(): string
    {
        return $this->payload()['text'];
    }

    public function state(): string
    {
        return $this->payload()['text'];
    }

    public function type(): string
    {
        return $this->payload()['type'];
    }

    public function issueNumber(): string
    {
        return $this->payload()['issueNumber'];
    }

    public function externalServiceId(): string
    {
        return $this->payload()['externalServiceId'];
    }

    public function originallyCreatedAt(): DateTimeImmutable
    {
        return (new DateTimeImmutable('@' . $this->payload()['originallyCreatedAt']))
            ->setTimezone(new DateTimeZone('UTC'));
    }

    /** @return string|null */
    public function reporterId()
    {
        return $this->payload()['reporterId'];
    }
}
