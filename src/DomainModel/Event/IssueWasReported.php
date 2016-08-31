<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasReported extends AggregateChanged
{
    public static function with(IssueId $id, CustomerId $reporterId, string $title, string $text)
    {
        return self::occur(
            (string) $id,
            ['title' => $title, 'text' => $text, 'reporterId' => (string) $reporterId]
        );
    }

    public function title(): string
    {
        return $this->payload()['title'];
    }

    public function text(): string
    {
        return $this->payload()['text'];
    }

    public function reporterId(): string
    {
        return $this->payload()['reporterId'];
    }
}
