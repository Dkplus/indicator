<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\ReporterId;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Issue
 */
class IssueSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('reportWith', [IssueId::generate(), ReporterId::generate(), 'Title', 'Text']);
    }

    function it_is_reported()
    {
        $this->shouldHaveRecorded(IssueWasReported::class);
    }

    function it_has_a_reporter()
    {
        $this->reporterId()->shouldHaveType(ReporterId::class);
    }
}
