<?php
declare(strict_types=1);
namespace feature\Dkplus\Indicator;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Gherkin\Node\PyStringNode;
use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
use Dkplus\Indicator\DomainModel\Event\IssueWasWithdrawn;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\IssueType;
use Dkplus\Indicator\Infrastructure\Repository\EventStoreFeedbackForum;
use Dkplus\Indicator\PhpSpec\Matcher\AggregateChangedMatcher;
use PhpSpec\Matcher\MatchersProvider;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\EventStore;

class CustomerContext implements Context, MatchersProvider
{
    /** @var Customer */
    private $myself;

    /** @var EventStore */
    private $eventStore;

    /** @var FeedbackForum */
    private $feedbackForum;

    /** @var IssueId */
    private $issueId;

    public function __construct()
    {
        $this->myself = Customer::register(CustomerId::generate(), 'Tom');
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
     * @When I report a/an :type to the feedback forum with title :title and text:
     */
    public function iReportAnIssueToTheFeedbackForumWithTitleAndText(string $type, string $title, PyStringNode $text)
    {
        $this->feedbackForum->add(
            $this->myself->reportIssue(IssueId::generate(), $title, $text->getRaw(), IssueType::fromString($type))
        );
    }

    /**
     * @Then the issue should have been reported to the feedback forum as :type
     */
    public function theIssueShouldHaveBeenReportedToTheFeedbackForumAs(string $type)
    {
        expect($this->eventStore)->toHaveRecorded(function (IssueWasReported $event) use ($type) {
            return $event->type() === $type;
        });
    }

    /**
     * @Then I should be the reporter of the issue
     */
    public function iShouldBeTheReporterOfTheIssue()
    {
        expect($this->eventStore)->toHaveRecorded(function (IssueWasReported $event) {
            return $this->myself->id()->equals($event->reporterId());
        });
    }

    /**
     * @Given an issue that has been reported by myself to the feedback forum
     */
    public function anIssueThatHasBeenReportedByMyself()
    {
        $this->issueId = IssueId::generate();
        $this->feedbackForum->add(
            $this->myself->reportIssue($this->issueId, 'Some title', 'Some text', IssueType::bugReport())
        );
    }

    /**
     * @When I withdraw the issue
     */
    public function iWithdrawTheIssue()
    {
        $this->feedbackForum->withId($this->issueId)->withdraw();
    }

    /**
     * @Then /^the issue should be withdrawn$/
     */
    public function theIssueShouldBeWithdrawn()
    {
        expect($this->eventStore)->toHaveRecorded(IssueWasWithdrawn::class);
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
}
