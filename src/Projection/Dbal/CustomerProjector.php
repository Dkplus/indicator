<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use Dkplus\Indicator\DomainModel\Event\CustomerWasRegistered;
use Doctrine\DBAL\Connection;

class CustomerProjector
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function onCustomerWasRegistered(CustomerWasRegistered $data)
    {
        $this->connection->insert(CustomerTable::TABLE_NAME, [
            'id' => $data->aggregateId(),
            'name' => $data->name(),
        ]);
    }
}
