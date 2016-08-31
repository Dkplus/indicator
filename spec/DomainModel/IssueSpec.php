<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\CustomerId;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Issue
 * @method void shouldHaveRecorded($event)
 */
class IssueSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('reportWith', [IssueId::generate(), CustomerId::generate(), 'Title', 'Text']);
    }

    function it_is_reported()
    {
        $this->shouldHaveRecorded(IssueWasReported::class);
    }

    function it_has_a_reporter()
    {
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }
}
