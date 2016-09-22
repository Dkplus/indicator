<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\ReportIssueHandler;
use Dkplus\Indicator\DomainModel\Customer;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Customers;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ReportIssueHandler
 */
class ReportIssueHandlerSpec extends ObjectBehavior
{
    function let(FeedbackForum $feedbackForum, Customers $customers)
    {
        $this->beConstructedWith($feedbackForum, $customers);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReportIssueHandler::class);
    }

    function it_reports_issues(FeedbackForum $feedbackForum, Customers $customers, Customer $customer, Issue $issue)
    {
        $command = ReportIssueBuilder::aReportIssueCommand()->build();

        $customers->withId(CustomerId::fromString($command->reporterId()))->willReturn($customer);
        $customer->reportIssue(Argument::cetera())->willReturn($issue);

        $this->__invoke($command);

        $feedbackForum->add($issue)->shouldHaveBeenCalled();
    }
}
