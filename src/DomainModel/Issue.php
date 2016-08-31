<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Prooph\EventSourcing\AggregateRoot;

class Issue extends AggregateRoot
{
    /** @var IssueId */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $text;

    /** @var ReporterId */
    private $reporterId;

    public static function reportWith(IssueId $id, ReporterId $reporterId, string $title, string $text)
    {
        $result = new self();
        $result->recordThat(IssueWasReported::with($id, $reporterId, $title, $text));
        return $result;
    }

    protected function aggregateId()
    {
        return (string) $this->id;
    }

    protected function whenIssueWasReported(IssueWasReported $event)
    {
        $this->id = IssueId::fromString($event->aggregateId());
        $this->title = $event->title();
        $this->text = $event->text();
        $this->reporterId = ReporterId::fromString($event->reporterId());
    }

    public function reporterId(): ReporterId
    {
        return $this->reporterId;
    }
}
