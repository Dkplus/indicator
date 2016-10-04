<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\DomainModel;

use DateTimeImmutable;
use Dkplus\Indicator\DomainModel\Event\IssueWasWithdrawn;
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

    function it is reported()
    {
        $this->shouldHaveRecorded(IssueWasReported::class);
    }

    function it can be imported from an external service()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement()]
        );
        $this->shouldHaveRecorded(IssueWasImported::class);
    }

    function it can have a reporter()
    {
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it has no reporter when imported from an external service()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement()]
        );
        $this->reporterId()->shouldBeNull();
    }

    function it is exported once to an external service()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasExported::class);
    }

    function it cannot be exported to an external service if its imported therefrom()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement()]
        );
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement());
        $this->shouldNotHaveRecorded(IssueWasExported::class);
    }

    function it can be recovered()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::reported(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
            CustomerId::generate(),
        ]);
        $this->shouldHaveRecorded(IssueWasRecovered::class);
        $this->reporterId()->shouldHaveType(CustomerId::class);
    }

    function it cannot be exported to an external service if its recovered therefrom()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::reported(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
            CustomerId::generate(),
        ]);
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement());
        $this->shouldNotHaveRecorded(IssueWasExported::class);
    }

    function it can be recovered without a reporter()
    {
        $this->beConstructedThrough('recoverFromExternalService', [
            IssueId::generate(),
            'Title',
            'Text',
            '4',
            '6',
            IssueState::reported(),
            IssueType::enhancement(),
            new DateTimeImmutable('10 days ago'),
        ]);
        $this->shouldHaveRecorded(IssueWasRecovered::class);
        $this->reporterId()->shouldBeNull();
    }

    function it can be rejected by an import()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::rejected(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::rejected(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasRejected::class);
    }

    function it can be implemented by an import()
    {
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::implemented(), IssueType::enhancement());
        $this->importFromExternalService('Title', 'Text', '4', '6', IssueState::implemented(), IssueType::enhancement());
        $this->shouldHaveRecordedOnce(IssueWasImplemented::class);
    }

    function it can be withdrawn if it has been reported()
    {
        $this->withdraw();
        $this->withdraw();
        $this->shouldHaveRecordedOnce(IssueWasWithdrawn::class);
    }

    function it cannot by closed if it has been imported()
    {
        $this->beConstructedThrough(
            'importFromAnExternalService',
            [IssueId::generate(), 'Title', 'Text', '4', '6', IssueState::reported(), IssueType::enhancement()]
        );
        $this->withdraw()->shouldThrow(IssueNotClosable::class);
    }
}
