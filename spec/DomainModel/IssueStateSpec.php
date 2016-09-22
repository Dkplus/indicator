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
    function it_can_be_opened()
    {
        $this->beConstructedThrough('opened');
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

    function it_can_be_closed()
    {
        $this->beConstructedThrough('closed');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('closed');
    }

    function it_can_be_reconstructed_from_string_as_opened()
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

    function it_can_be_reconstructed_from_string_as_closed()
    {
        $this->beConstructedThrough('fromString', ['closed']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('closed');
    }

    function it_can_be_equal_to_another_state()
    {
        $this->beConstructedThrough('opened');
        $this->equals(IssueState::opened())->shouldBe(true);
        $this->equals(IssueState::closed())->shouldBe(false);
    }

    function it_is_open_when_its_opened()
    {
        $this->beConstructedThrough('opened');
        $this->isOpen()->shouldBe(true);
    }

    function it_is_not_open_when_its_closed()
    {
        $this->beConstructedThrough('closed');
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
