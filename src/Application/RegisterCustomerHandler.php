<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Customers;

class RegisterCustomerHandler
{
    /** @var Customers */
    private $customers;

    public function __construct(Customers $customers)
    {
        $this->customers = $customers;
    }

    public function __invoke(RegisterCustomer $data)
    {
        $this->customers->add(Customer::register(CustomerId::fromString($data->customerId()), $data->name()));
    }
}
