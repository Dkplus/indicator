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
    public function findOpen(): array
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
            ->leftJoin('issue', CustomerTable::TABLE_NAME, 'customer', 'issue.reporter_id = customer.id')
            ->where('issue.open = 1')
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
            if ($row['reporter_id']) {
                $result->reporter = new CustomerProjection();
                $result->reporter->id = $row['reporter_id'];
                $result->reporter->name = $row['reporter_name'];
            }
            return $result;
        }, $statement->fetchAll());
    }

    /**
     * @return IssueProjection[]
     */
    public function findClosed(): array
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
            ->leftJoin('issue', CustomerTable::TABLE_NAME, 'customer', 'issue.reporter_id = customer.id')
            ->where('issue.open = 0')
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
            if ($row['reporter_id']) {
                $result->reporter = new CustomerProjection();
                $result->reporter->id = $row['reporter_id'];
                $result->reporter->name = $row['reporter_name'];
            }
            return $result;
        }, $statement->fetchAll());
    }

    /** @return IssueProjection|null */
    public function findOneById(string $id)
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
            ->leftJoin('issue', CustomerTable::TABLE_NAME, 'customer', 'issue.reporter_id = customer.id')
            ->where('issue.id = :id')
            ->setParameter('id', $id)
            ->execute();

        $results = array_map(function (array $row) {
            $result = new IssueProjection();
            $result->id = $row['id'];
            $result->title = $row['title'];
            $result->text = $row['text'];
            $result->state = $row['state'];
            $result->externalServiceId = $row['external_service_id'];
            $result->issueNumber = $row['issue_number'];
            $result->updatedAt = new DateTimeImmutable($row['updated_at'], new DateTimeZone('UTC'));
            if ($row['reporter_id']) {
                $result->reporter = new CustomerProjection();
                $result->reporter->id = $row['reporter_id'];
                $result->reporter->name = $row['reporter_name'];
            }
            return $result;
        }, $statement->fetchAll());
        return current($results) ?? null;
    }
}
