<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueType;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasReported extends AggregateChanged
{
    public static function with(IssueId $id, CustomerId $reporterId, string $title, string $text, IssueType $type)
    {
        return self::occur(
            (string) $id,
            ['title' => $title, 'text' => $text, 'reporterId' => (string) $reporterId, 'type' => (string) $type]
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

    public function type(): string
    {
        return $this->payload()['type'];
    }

    public function reporterId(): string
    {
        return $this->payload()['reporterId'];
    }
}
