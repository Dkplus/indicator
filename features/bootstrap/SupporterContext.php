<?php
declare(strict_types=1);
namespace feature\Dkplus\Indicator;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Gherkin\Node\PyStringNode;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
use Dkplus\Indicator\DomainModel\Event\IssueWasWithdrawn;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueState;
use Dkplus\Indicator\DomainModel\IssueType;
use Dkplus\Indicator\Infrastructure\Repository\EventStoreFeedbackForum;
use Dkplus\Indicator\PhpSpec\Matcher\AggregateChangedMatcher;
use PhpSpec\Matcher\MatchersProvider;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\EventStore;

class SupporterContext implements Context, MatchersProvider
{
    /** @var EventStore */
    private $eventStore;

    /** @var FeedbackForum */
    private $feedbackForum;

    /** @var IssueId */
    private $issueId;

    public function __construct()
    {
        $this->eventStore = new EventStore(new InMemoryAdapter(), new ProophActionEventEmitter());
        $this->feedbackForum = EventStoreFeedbackForum::inMemory($this->eventStore);
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
        $this->feedbackForum->addPendingEventsToStream();
    }

    public function getMatchers()
    {
        return [new AggregateChangedMatcher()];
    }

    /**
     * @Given /^the issue should be closed$/
     */
    public function theIssueShouldBeClosed()
    {
        expect($this->eventStore)->toHaveRecorded(function ($event) {
            return $event instanceof IssueWasWithdrawn
            || $event instanceof IssueWasRejected
            || $event instanceof IssueWasImplemented;
        });
    }

    /**
     * @Given an issue that has been reported
     */
    public function anIssueThatHasBeenReported()
    {
        $this->issueId = IssueId::generate();
        $this->feedbackForum->add(Issue::reportWith(
            $this->issueId,
            CustomerId::generate(),
            'Any title',
            'Any text',
            IssueType::bugReport()
        ));
    }

    /**
     * @When I implement the issue
     */
    public function iImplementTheIssue()
    {
        $this->feedbackForum->withId($this->issueId)->importFromExternalService(
            'Any title',
            'Any text',
            '3',
            '4',
            IssueState::implemented(),
            IssueType::bugReport()
        );
    }

    /**
     * @Then the issue should be implemented
     */
    public function theIssueShouldBeImplemented()
    {
        expect($this->eventStore)->toHaveRecorded(IssueWasImplemented::class);
    }

    /**
     * @When I reject the issue
     */
    public function iRejectTheIssue()
    {
        $this->feedbackForum->withId($this->issueId)->importFromExternalService(
            'Any title',
            'Any text',
            '3',
            '4',
            IssueState::rejected(),
            IssueType::bugReport()
        );
    }

    /**
     * @Then the issue should be rejected
     */
    public function theIssueShouldBeRejected()
    {
        expect($this->eventStore)->toHaveRecorded(IssueWasRejected::class);
    }
}