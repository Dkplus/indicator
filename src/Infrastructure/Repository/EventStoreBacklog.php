<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Infrastructure\Repository;

use Dkplus\Indicator\DomainModel\Backlog;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Snapshot\SnapshotStore;
use Prooph\EventStore\Stream\StreamName;

class EventStoreBacklog extends AggregateRepository implements Backlog
{
    public static function inMemory(EventStore $eventStore)
    {
        return new self($eventStore, null, null, true);
    }

    public function __construct(
        EventStore $eventStore,
        SnapshotStore $snapshotStore = null,
        StreamName $streamName = null,
        $oneStreamPerAggregate = false
    ) {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Issue::class),
            new AggregateTranslator(),
            $streamName,
            $snapshotStore,
            $oneStreamPerAggregate
        );
    }

    public function get(IssueId $issueId)
    {
        return $this->getAggregateRoot((string) $issueId);
    }

    public function add(Issue $issue)
    {
        $this->addAggregateRoot($issue);
    }
}
