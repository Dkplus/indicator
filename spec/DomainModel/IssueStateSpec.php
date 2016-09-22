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
    function it_can_be_reported()
    {
        $this->beConstructedThrough('reported');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('reported');
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

    function it_can_be_withdrawn()
    {
        $this->beConstructedThrough('withdrawn');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('withdrawn');
    }

    function it_can_be_reconstructed_from_string_as_reported()
    {
        $this->beConstructedThrough('fromString', ['reported']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('reported');
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

    function it_can_be_reconstructed_from_string_as_withdrawn()
    {
        $this->beConstructedThrough('fromString', ['withdrawn']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('withdrawn');
    }

    function it_can_be_equal_to_another_state()
    {
        $this->beConstructedThrough('reported');
        $this->equals(IssueState::reported())->shouldBe(true);
        $this->equals(IssueState::withdrawn())->shouldBe(false);
    }

    function it_is_open_when_its_reported()
    {
        $this->beConstructedThrough('reported');
        $this->isOpen()->shouldBe(true);
    }

    function it_is_not_open_when_its_withdrawn()
    {
        $this->beConstructedThrough('withdrawn');
        $this->isOpen()->shouldBe(false);
    }

    function it_is_not_open_when_its_rejected()
    {
        $this->beConstructedThrough('rejected');
        $this->isOpen()->shouldBe(false);
    }

    function it_is_not_open_when_its_implemented()
    {
        $this->beConstructedThrough('implemented');
        $this->isOpen()->shouldBe(false);
    }
}
