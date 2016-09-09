<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Event\IssueWasExportedToExternalService;
use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
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

    /** @var CustomerId|null */
    private $reporterId;

    /** @var string */
    private $issueNumber;

    /** @var string */
    private $externalServiceId;

    /** @var IssueState */
    private $state;

    public static function importFromExternalService(
        IssueId $issueId,
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        CustomerId $customerId = null
    ) {
        $result = new self();
        $result->recordThat(IssueWasImported::fromExternalService(
            $issueId,
            $title,
            $text,
            $issueNumber,
            $externalServiceId,
            $state,
            $customerId
        ));
        return $result;
    }

    public static function reportWith(IssueId $id, CustomerId $reporterId, string $title, string $text)
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
        $this->reporterId = CustomerId::fromString($event->reporterId());
    }

    protected function whenIssueWasImported(IssueWasImported $event)
    {
        $this->id = IssueId::fromString($event->aggregateId());
        $this->title = $event->title();
        $this->text = $event->text();
        $this->reporterId = $event->customerId() !== null ? CustomerId::fromString($event->customerId()) : null;
        $this->issueNumber = $event->issueNumber();
        $this->externalServiceId = $event->externalServiceId();
        $this->state = IssueState::fromString($event->state());
    }

    /** @return CustomerId|null */
    public function reporterId()
    {
        return $this->reporterId;
    }

    public function exportToExternalService(string $issueNumber, string $externalServiceId)
    {
        $this->recordThat(IssueWasExportedToExternalService::withIssueNumberAndExternalServiceId(
            $this->id,
            $issueNumber,
            $externalServiceId
        ));
    }

    public function whenIssueWasExportedToExternalService(IssueWasExportedToExternalService $event)
    {
        $this->issueNumber = $event->issueNumber();
        $this->externalServiceId = $event->externalServiceId();
    }
}
