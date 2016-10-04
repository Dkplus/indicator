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
    function it can be reported()
    {
        $this->beConstructedThrough('reported');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('reported');
    }

    function it can be implemented()
    {
        $this->beConstructedThrough('implemented');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('implemented');
    }

    function it can be rejected()
    {
        $this->beConstructedThrough('rejected');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('rejected');
    }

    function it can be withdrawn()
    {
        $this->beConstructedThrough('withdrawn');
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('withdrawn');
    }

    function it can be reconstructed from string as reported()
    {
        $this->beConstructedThrough('fromString', ['reported']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('reported');
    }

    function it can be reconstructed from string as implemented()
    {
        $this->beConstructedThrough('fromString', ['implemented']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('implemented');
    }

    function it can be reconstructed from string as rejected()
    {
        $this->beConstructedThrough('fromString', ['rejected']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('rejected');
    }

    function it can be reconstructed from string as withdrawn()
    {
        $this->beConstructedThrough('fromString', ['withdrawn']);
        $this->shouldHaveType(IssueState::class);
        $this->shouldBeLike('withdrawn');
    }

    function it can be equal to another state()
    {
        $this->beConstructedThrough('reported');
        $this->equals(IssueState::reported())->shouldBe(true);
        $this->equals(IssueState::withdrawn())->shouldBe(false);
    }

    function it is open when its reported()
    {
        $this->beConstructedThrough('reported');
        $this->isOpen()->shouldBe(true);
    }

    function it is not open when its withdrawn()
    {
        $this->beConstructedThrough('withdrawn');
        $this->isOpen()->shouldBe(false);
    }

    function it is not open when its rejected()
    {
        $this->beConstructedThrough('rejected');
        $this->isOpen()->shouldBe(false);
    }

    function it is not open when its implemented()
    {
        $this->beConstructedThrough('implemented');
        $this->isOpen()->shouldBe(false);
    }
}
