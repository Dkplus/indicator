<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\IssueState;
use PhpSpec\ObjectBehavior;

/**
 * @mixin IssueState
 */
class IssueStateSpec extends ObjectBehavior
{
    function it_can_be_open()
    {
        $this->beConstructedThrough('open');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('open');
    }

    function it_can_be_implemented()
    {
        $this->beConstructedThrough('implemented');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('implemented');
    }

    function it_can_be_rejected()
    {
        $this->beConstructedThrough('rejected');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('rejected');
    }

    function it_can_be_reconstructed_from_string_as_open()
    {
        $this->beConstructedThrough('fromString', ['open']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('open');
    }

    function it_can_be_reconstructed_from_string_as_implemented()
    {
        $this->beConstructedThrough('fromString', ['implemented']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('implemented');
    }

    function it_can_be_reconstructed_from_string_as_rejected()
    {
        $this->beConstructedThrough('fromString', ['rejected']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('rejected');
    }
}
