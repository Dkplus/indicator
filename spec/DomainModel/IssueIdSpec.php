<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\IssueId;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

/**
 * @mixin IssueId
 */
class IssueIdSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('generate');
    }

    function it is initializable()
    {
        $this->shouldHaveType(IssueId::class);
    }

    function it can be generated from string()
    {
        $idAsString = Uuid::uuid4()->toString();
        $this->beConstructedThrough('fromString', [$idAsString]);
        $this->shouldBeLike($idAsString);
    }
}
