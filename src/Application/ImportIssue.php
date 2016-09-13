<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\UuidInterface;

class ImportIssue extends Command  implements PayloadConstructable
{
    use PayloadTrait;

    public static function fromExternalService(
        UuidInterface $issueId,
        string $title,
        string $text,
        string $state,
        string $type,
        string $issueNumber,
        string $externalServiceId
    ): self {
        return new self([
            'issueId' => $issueId->toString(),
            'title' => $title,
            'text' => $text,
            'state' => $state,
            'type' => $type,
            'issueNumber' => $issueNumber,
            'externalServiceId' => $externalServiceId,
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
        return $this->payload()['state'];
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
}
