<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use DateTimeImmutable;
use Dkplus\Indicator\DomainModel\Event\IssueWasClosed;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasExported;
use Dkplus\Indicator\DomainModel\Event\IssueWasRecovered;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueNotClosable;
use Dkplus\Indicator\DomainModel\IssueState;
use Dkplus\Indicator\DomainModel\IssueType;
use PhpSpec\ObjectBehavior;

/**
 * @mixin Issue
 * @method void shouldHaveRecorded($event)
 * @method void shouldNotHaveRecorded($event)
 * @method void shouldHaveRecordedOnce($event)
 */
class IssueSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough(
            'reportWith',
            [IssueId::generate(), CustomerId::generate(), 'Title', 'Text', IssueType::enhancement()]
        );
    }

    function it_is_reported()
    {
        $this->shouldHaveRecorded(IssueWasReported::class);
    }

    function it_can_be_imported_from_an_external_service()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement()]
        );
        $this->shouldHaveRecorded(IssueWasImported::class);
    }

    function it_can_have_a_reporter()
    {
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it_has_no_reporter_when_imported_from_an_external_service()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement()]
        );
        $this->reporterId()->shouldBeNull();
    }

    function it_is_exported_once_to_an_external_service()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasExported::class);
    }

    function it_cannot_be_exported_to_an_external_service_if_its_imported_therefrom()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement()]
        );
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement());
        $this->shouldNotHaveRecorded(IssueWasExported::class);
    }

    function it_can_be_recovered()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::opened(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
            CustomerId::generate(),
        ]);
        $this->shouldHaveRecorded(IssueWasRecovered::class);
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it_cannot_be_exported_to_an_external_service_if_its_recovered_therefrom()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::opened(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
            CustomerId::generate(),
        ]);
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement());
        $this->shouldNotHaveRecorded(IssueWasExported::class);
    }

    function it_can_be_recovered_without_a_reporter()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::opened(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
        ]);
        $this->shouldHaveRecorded(IssueWasRecovered::class);
        $this->reporterId()->shouldBeNull();
    }

    function it_can_be_rejected_by_an_import()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::rejected(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::rejected(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasRejected::class);
    }

    function it_can_be_implemented_by_an_import()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::implemented(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::implemented(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasImplemented::class);
    }

    function it_can_be_closed_if_it_has_been_reported()
    {
        $this->close();
        $this->close();
        $this->shouldHaveRecordedOnce(IssueWasClosed::class);
    }

    function it_cannot_by_closed_if_it_has_been_imported()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::opened(), IssueType::enhancement()]
        );
        $this->close()->shouldThrow(IssueNotClosable::class);
    }
}
