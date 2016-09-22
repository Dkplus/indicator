<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\RegisterCustomerHandler;
use Dkplus\Indicator\DomainModel\Customers;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin RegisterCustomerHandler
 */
class RegisterCustomerHandlerSpec extends ObjectBehavior
{
    function let(Customers $customers)
    {
        $this->beConstructedWith($customers);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RegisterCustomerHandler::class);
    }

    function it_registers_customers(Customers $customers)
    {
        $this->__invoke(RegisterCustomerBuilder::aRegisterCustomerCommand()->build());

        $customers->add(Argument::any())->shouldHaveBeenCalled();
    }
}
