<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Connection;

class IssueFinder
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return IssueProjection[]
     */
    public function findAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'issue.id',
                'issue.title',
                'issue.updated_at',
                'issue.text',
                'issue.state',
                'issue.issue_number',
                'issue.external_service_id',
                'customer.id AS reporter_id',
                'customer.name AS reporter_name'
            )
            ->from(IssueTable::TABLE_NAME, 'issue')
            ->join('issue', CustomerTable::TABLE_NAME, 'customer', 'issue.reporter_id = customer.id')
            ->orderBy('issue.updated_at', 'DESC')
            ->execute();
        return array_map(function (array $row) {
            $result = new IssueProjection();
            $result->id = $row['id'];
            $result->title = $row['title'];
            $result->text = $row['text'];
            $result->state = $row['state'];
            $result->externalServiceId = $row['external_service_id'];
            $result->issueNumber = $row['issue_number'];
            $result->updatedAt = new DateTimeImmutable($row['updated_at'], new DateTimeZone('UTC'));
            $result->reporter = new CustomerProjection();
            $result->reporter->id = $row['reporter_id'];
            $result->reporter->name = $row['reporter_name'];
            return $result;
        }, $statement->fetchAll());
    }
}
