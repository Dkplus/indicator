<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\UuidInterface;

class ReportIssue extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withTitleAndText(
        string $customerId,
        UuidInterface $issueId,
        string $title,
        string $text,
        string $type
    ): self {
        return new self([
            'reporterId' => $customerId,
            'issueId' => $issueId->toString(),
            'title' => $title,
            'text' => $text,
            'type' => $type,
        ]);
    }

    public function reporterId(): string
    {
        return $this->payload()['reporterId'];
    }

    public function issueId(): string
    {
        return $this->payload()['issueId'];
    }

    public function title(): string
    {
        return $this->payload()['title'];
    }

    public function type(): string
    {
        return $this->payload()['type'];
    }

    public function text(): string
    {
        return $this->payload()['text'];
    }
}
