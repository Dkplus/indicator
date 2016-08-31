<?php
declare(strict_types=1);
namespace feature\Dkplus\Indicator;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Gherkin\Node\PyStringNode;
use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\Infrastructure\Repository\EventStoreFeedbackForum;
use Dkplus\Indicator\PhpSpec\Matcher\AggregateChangedRecorderMatcher;
use PhpSpec\Matcher\MatchersProvider;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\EventStore;

class CustomerContext implements SnippetAcceptingContext, MatchersProvider
{
    /** @var Customer */
    private $myself;

    /** @var EventStore */
    private $eventStore;

    /** @var FeedbackForum */
    private $feedbackForum;

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
    }

    /**
     * @When I report an issue to the feedback forum with title :title and text:
     */
    public function iReportAnIssueToTheFeedbackForumWithTitleAndText(string $title, PyStringNode $text)
    {
        $this->feedbackForum->add($this->myself->reportIssue(IssueId::generate(), $title, $text->getRaw()));
    }

    /**
     * @Then the issue should have been reported to the feedback forum
     */
    public function theIssueShouldHaveBeenReportedToTheFeedbackForum()
    {
        expect($this->eventStore)->toHaveRecorded(IssueWasReported::class);
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

    public function getMatchers()
    {
        return [new AggregateChangedRecorderMatcher()];
    }
}
