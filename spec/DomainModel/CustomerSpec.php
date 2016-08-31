<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Event\CustomerWasRegistered;
use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\CustomerId;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Customer
 * @method void shouldHaveRecorded($event)
 */
class CustomerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('register', [CustomerId::generate(), 'Tom']);
    }

    function it_is_registered()
    {
        $this->shouldHaveType(Customer::class);
        $this->shouldHaveRecorded(CustomerWasRegistered::class);
    }

    function it_has_an_id()
    {
        $this->id()->shouldHaveType(CustomerId::class);
    }
}
