<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Infrastructure\Repository;

use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueNotFound;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;

class EventStoreFeedbackForum extends AggregateRepository implements FeedbackForum
{
    public static function inMemory(EventStore $eventStore)
    {
        return new self(
            $eventStore,
            AggregateType::fromAggregateRootClass(Issue::class),
            new AggregateTranslator(),
            null,
            null,
            true
        );
    }

    public function withId(IssueId $issueId): Issue
    {
        $result = $this->getAggregateRoot((string) $issueId); /* @var $result Issue|null */
        if (! $result) {
            throw IssueNotFound::withId((string) $issueId);
        }
        return $result;
    }

    public function add(Issue $issue)
    {
        $this->addAggregateRoot($issue);
    }
}
