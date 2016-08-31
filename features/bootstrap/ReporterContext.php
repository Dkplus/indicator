<?php
declare(strict_types=1);
namespace feature\Dkplus\Indicator;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Gherkin\Node\PyStringNode;
use Dkplus\Indicator\DomainModel\Backlog;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\ReporterId;
use Dkplus\Indicator\Infrastructure\Repository\EventStoreBacklog;
use Dkplus\Indicator\PhpSpec\Matcher\AggregateChangedRecorderMatcher;
use PhpSpec\Matcher\MatchersProvider;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\EventStore;

class ReporterContext implements SnippetAcceptingContext, MatchersProvider
{
    /** @var ReporterId */
    private $reporterId;

    /** @var EventStore */
    private $eventStore;

    /** @var Backlog */
    private $backlog;

    /** @var IssueId|null */
    private $lastIssueId;

    public function __construct()
    {
        $this->reporterId = ReporterId::generate();
        $this->eventStore = new EventStore(new InMemoryAdapter(), new ProophActionEventEmitter());
        $this->backlog = EventStoreBacklog::inMemory($this->eventStore);
    }

    /**
     * @BeforeScenario
     */
    public function startTransaction()
    {
        $this->eventStore->beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function commitTransaction()
    {
        $this->eventStore->commit();
    }

    /**
     * @AfterStep
     */
    public function startRecording(AfterStepScope $scope)
    {
        if ($scope->getStep()->getKeywordType() === 'Given') {
            $this->eventStore->commit();
            $this->eventStore->beginTransaction();
        }
    }

    /**
     * @When I report an issue with title :title and text:
     */
    public function iReportAnIssueWithTitleAndText(string $title, PyStringNode $text)
    {
        $this->lastIssueId = IssueId::generate();
        $issue = Issue::reportWith($this->lastIssueId, $this->reporterId, $title, $text->getRaw());
        $this->backlog->add($issue);
    }

    /**
     * @Then the issue should have been reported
     */
    public function theIssueShouldHaveBeenReported()
    {
        expect($this->eventStore)->toHaveRecorded(IssueWasReported::class);
    }

    /**
     * @Then I should be the reporter of the issue
     */
    public function iShouldBeTheReporterOfTheIssue()
    {
        expect($this->eventStore)->toHaveRecorded(function (IssueWasReported $event) {
            return $this->reporterId->equals($event->reporterId());
        });
    }

    public function getMatchers()
    {
        return [new AggregateChangedRecorderMatcher()];
    }
}
