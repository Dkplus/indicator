<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use Dkplus\Indicator\DomainModel\Event\IssueWasClosed;
use Dkplus\Indicator\DomainModel\Event\IssueWasExported;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasRecovered;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class IssueProjector
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function onIssueWasReported(IssueWasReported $data)
    {
        $this->connection->insert(IssueTable::TABLE_NAME, [
            'id' => $data->aggregateId(),
            'reporter_id' => $data->reporterId(),
            'title' => $data->title(),
            'text' => $data->text(),
            'state' => 'opened',
            'open' => true,
            'external_service_id' => '',
            'issue_number' => '',
            'type' => $data->type(),
            'updated_at' => $data->createdAt()
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasImported(IssueWasImported $data)
    {
        $this->connection->insert(IssueTable::TABLE_NAME, [
            'id' => $data->aggregateId(),
            'title' => $data->title(),
            'text' => $data->text(),
            'external_service_id' => $data->externalServiceId(),
            'issue_number' => $data->issueNumber(),
            'state' => $data->state(),
            'open' => $data->open(),
            'type' => $data->type(),
            'updated_at' => $data->createdAt(),
            'reporter_id' => '',
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasExported(IssueWasExported $data)
    {
        $this->connection->update(IssueTable::TABLE_NAME, [
            'external_service_id' => $data->externalServiceId(),
            'issue_number' => $data->issueNumber(),
        ], [
            'id' => $data->aggregateId()
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasRecovered(IssueWasRecovered $data)
    {
        $this->connection->insert(IssueTable::TABLE_NAME, [
            'id' => $data->aggregateId(),
            'reporter_id' => $data->reporterId() ?: '',
            'title' => $data->title(),
            'text' => $data->text(),
            'external_service_id' => $data->externalServiceId(),
            'issue_number' => $data->issueNumber(),
            'state' => $data->state(),
            'open' => $data->open(),
            'type' => $data->type(),
            'updated_at' => $data->originallyCreatedAt(),
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasClosed(IssueWasClosed $data)
    {
        $this->connection->update(IssueTable::TABLE_NAME, [
            'state' => 'closed',
            'open' => false,
            'updated_at' => $data->createdAt(),
        ], [
            'id' => $data->aggregateId(),
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasImplemented(IssueWasImplemented $data)
    {
        $this->connection->update(IssueTable::TABLE_NAME, [
            'state' => 'implemented',
            'open' => false,
            'updated_at' => $data->createdAt(),
        ], [
            'id' => $data->aggregateId()
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }

    public function onIssueWasRejected(IssueWasRejected $data)
    {
        $this->connection->update(IssueTable::TABLE_NAME, [
            'state' => 'rejected',
            'open' => false,
            'updated_at' => $data->createdAt(),
        ], [
            'id' => $data->aggregateId()
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }
}
