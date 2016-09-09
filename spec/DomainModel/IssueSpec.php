<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasExportedToExternalService;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueState;
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

    function it_can_be_imported_from_an_external_service()
    {
        $this->beConstructedThrough(
            'importFromExternalService',
            [IssueId::generate(), 'Title', 'Text', 4, 6, IssueState::open(), CustomerId::generate()]
        );
        $this->shouldHaveRecorded(IssueWasImported::class);
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it_has_a_reporter()
    {
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it_may_not_have_a_reporter_when_imported_from_an_external_service()
    {
        $this->beConstructedThrough(
            'importFromExternalService',
            [IssueId::generate(), 'Title', 'Text', 4, 6, IssueState::open(), null]
        );
        $this->reporterId()->shouldBeNull();
    }

    function it_is_exported_to_an_external_service()
    {
        $this->exportToExternalService(4, 6);
        $this->shouldHaveRecorded(IssueWasExportedToExternalService::class);
    }
}
