<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use DateTimeImmutable;
use Dkplus\Indicator\DomainModel\Event\IssueWasClosed;
use Dkplus\Indicator\DomainModel\Event\IssueWasExported;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasRecovered;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
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

    /** @var IssueType */
    private $type;

    public static function recoverFromExternalService(
        IssueId $issueId,
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        IssueType $type,
        DateTimeImmutable $originallyCreatedAt,
        CustomerId $reporterId = null
    ): self {
        $result = new self();
        $result->recordThat(IssueWasRecovered::fromExternalService(
            $issueId,
            $title,
            $text,
            $issueNumber,
            $externalServiceId,
            $state,
            $type,
            $originallyCreatedAt,
            $reporterId
        ));
        return $result;
    }

    public static function importFromAnExternalService(
        IssueId $issueId,
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        IssueType $type
    ): self {
        $result = new self();
        $result->recordThat(IssueWasImported::fromExternalService(
            $issueId,
            $title,
            $text,
            $issueNumber,
            $externalServiceId,
            $state,
            $type
        ));
        return $result;
    }

    public static function reportWith(
        IssueId $id,
        CustomerId $reporterId,
        string $title,
        string $text,
        IssueType $type
    ): self {
        $result = new self();
        $result->recordThat(IssueWasReported::with($id, $reporterId, $title, $text, $type));
        return $result;
    }

    protected function aggregateId()
    {
        return (string) $this->id;
    }

    public function importFromExternalService(
        string $title,
        string $text,
        string $issueNumber,
        string $externalServiceId,
        IssueState $state,
        IssueType $type
    ) {
        if ($this->issueNumber === null
            && $this->externalServiceId === null
        ) {
            $this->recordThat(IssueWasExported::toExternalService(
                $this->id,
                $issueNumber,
                $externalServiceId
            ));
        }
        if (! $this->state->equals($state)) {
            if ($state->equals(IssueState::implemented())) {
                $this->recordThat(IssueWasImplemented::bySystem($this->id));
            } elseif ($state->equals(IssueState::rejected())) {
                $this->recordThat(IssueWasRejected::bySystem($this->id));
            }
        }
    }

    protected function whenIssueWasReported(IssueWasReported $event)
    {
        $this->id = IssueId::fromString($event->aggregateId());
        $this->title = $event->title();
        $this->text = $event->text();
        $this->reporterId = CustomerId::fromString($event->reporterId());
        $this->type = IssueType::fromString($event->type());
        $this->state = IssueState::opened();
    }

    protected function whenIssueWasImported(IssueWasImported $event)
    {
        $this->id = IssueId::fromString($event->aggregateId());
        $this->title = $event->title();
        $this->text = $event->text();
        $this->issueNumber = $event->issueNumber();
        $this->externalServiceId = $event->externalServiceId();
        $this->state = IssueState::fromString($event->state());
        $this->type = IssueType::fromString($event->type());
    }

    protected function whenIssueWasRecovered(IssueWasRecovered $event)
    {
        $this->id = IssueId::fromString($event->aggregateId());
        $this->reporterId = $event->reporterId() ? CustomerId::fromString($event->reporterId()) : null;
        $this->title = $event->title();
        $this->text = $event->text();
        $this->issueNumber = $event->issueNumber();
        $this->externalServiceId = $event->externalServiceId();
        $this->state = IssueState::fromString($event->state());
        $this->type = IssueType::fromString($event->type());
    }

    protected function whenIssueWasExported(IssueWasExported $event)
    {
        $this->issueNumber = $event->issueNumber();
        $this->externalServiceId = $event->externalServiceId();
    }

    /** @return CustomerId|null */
    public function reporterId()
    {
        return $this->reporterId;
    }

    public function close()
    {
        if ($this->reporterId && $this->state->isOpen()) {
            $this->recordThat(IssueWasClosed::byUser($this->id, $this->reporterId));
        }
    }

    protected function whenIssueWasClosed()
    {
        $this->state = IssueState::closed();
    }

    protected function whenIssueWasImplemented()
    {
        $this->state = IssueState::implemented();
    }

    protected function whenIssueWasRejected()
    {
        $this->state = IssueState::rejected();
    }
}
