<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\RecoverIssueHandler;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin RecoverIssueHandler
 */
class RecoverIssueHandlerSpec extends ObjectBehavior
{
    function let(FeedbackForum $feedbackForum)
    {
        $this->beConstructedWith($feedbackForum);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecoverIssueHandler::class);
    }

    function it_recovers_issues_reported_by_the_system(FeedbackForum $feedbackForum)
    {
        $this->__invoke(RecoverIssueBuilder::aRecoverIssueCommand()->build());

        $feedbackForum->add(Argument::any())->shouldHaveBeenCalled();
    }

    function it_recovers_issues_reported_by_a_customer(FeedbackForum $feedbackForum)
    {

        $this->__invoke(RecoverIssueBuilder::aRecoverIssueCommand()->withAnyCustomerId()->build());

        $feedbackForum->add(Argument::any())->shouldHaveBeenCalled();
    }
}
