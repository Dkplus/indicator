<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;

class ReportIssue extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withTitleAndText(Uuid $customerId, Uuid $issueId, string $title, string $text): self
    {
        return new self([
            'reporterId' => (string) $customerId,
            'issueId' => (string) $issueId,
            'title' => $title,
            'text' => $text,
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

    public function text(): string
    {
        return $this->payload()['text'];
    }
}
