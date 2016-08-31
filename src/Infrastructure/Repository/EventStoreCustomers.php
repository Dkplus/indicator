<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Infrastructure\Repository;

use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Customers;
use Prooph\EventStore\Aggregate\AggregateRepository;

class EventStoreCustomers extends AggregateRepository implements Customers
{
    public function withId(CustomerId $customerId): Customer
    {
        return $this->getAggregateRoot((string) $customerId);
    }

    public function add(Customer $customer)
    {
        $this->addAggregateRoot($customer);
    }
}
