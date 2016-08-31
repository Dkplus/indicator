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

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerId::class);
    }

    function it_can_be_generated_from_string()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);
        $this->shouldBeLike($idAsString);
    }

    function it_is_comparable_to_other_reporter_ids()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);

        $this->equals(CustomerId::fromString($idAsString))->shouldBe(true);
        $this->equals(CustomerId::generate())->shouldBe(false);
    }

    function it_is_comparable_to_reporter_ids_as_string()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);

        $this->equals($idAsString)->shouldBe(true);
        $this->equals(Uuid::uuid4()->toString())->shouldBe(false);
    }
}
