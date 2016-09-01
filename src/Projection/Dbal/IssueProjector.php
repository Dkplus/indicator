<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

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
            'updated_at' => $data->createdAt()
        ], [
            'updated_at' => Type::DATETIME
        ]);
    }
}
