<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\CustomerId;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

/**
 * @mixin CustomerId
 */
class CustomerIdSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('generate');
    }

    function it is initializable()
    {
        $this->shouldHaveType(CustomerId::class);
    }

    function it can be generated from string()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);
        $this->shouldBeLike($idAsString);
    }

    function it is comparable to other reporter ids()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);

        $this->equals(CustomerId::fromString($idAsString))->shouldBe(true);
        $this->equals(CustomerId::generate())->shouldBe(false);
    }

    function it is comparable to reporter ids as string()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);

        $this->equals($idAsString)->shouldBe(true);
        $this->equals(Uuid::uuid4()->toString())->shouldBe(false);
    }
}
