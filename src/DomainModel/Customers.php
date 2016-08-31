<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

interface Customers
{
    public function withId(CustomerId $customerId): Customer;
    public function add(Customer $customer);
}
