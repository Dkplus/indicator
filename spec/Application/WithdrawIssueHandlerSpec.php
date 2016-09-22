<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\WithdrawIssueHandler;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueNotClosable;
use PhpSpec\ObjectBehavior;

/**
 * @mixin WithdrawIssueHandler
 */
class WithdrawIssueHandlerSpec extends ObjectBehavior
{
    function let(FeedbackForum $feedbackForum)
    {
        $this->beConstructedWith($feedbackForum);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(WithdrawIssueHandler::class);
    }

    function it_closes_issues(FeedbackForum $feedbackForum, Issue $issue)
    {
        $command = WithdrawIssueBuilder::aWithdrawnIssueCommand()->build();

        $feedbackForum->withId(IssueId::fromString($command->issueId()))->willReturn($issue);
        $issue->reporterId()->willReturn(CustomerId::fromString($command->customerId()));
        $issue->withdraw()->shouldBeCalled();

        $this->__invoke($command);
    }

    function it_allows_closing_only_from_the_reporting_user(FeedbackForum $feedbackForum, Issue $issue)
    {
        $command = WithdrawIssueBuilder::aWithdrawnIssueCommand()->build();

        $feedbackForum->withId(IssueId::fromString($command->issueId()))->willReturn($issue);
        $issue->reporterId()->willReturn(CustomerId::generate());
        $issue->withdraw()->shouldNotBeCalled();

        $this->shouldThrow(IssueNotClosable::class)->during('__invoke', [$command]);
    }
}
