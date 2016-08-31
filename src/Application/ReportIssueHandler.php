<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Customers;

class ReportIssueHandler
{
    /** @var FeedbackForum */
    private $feedbackForum;

    /** @var Customers */
    private $reporters;

    public function __construct(FeedbackForum $backlog, Customers $reporters)
    {
        $this->feedbackForum = $backlog;
        $this->reporters = $reporters;
    }

    public function __invoke(ReportIssue $command)
    {
        $issue = $this->reporters
            ->withId(CustomerId::fromString($command->reporterId()))
            ->reportIssue(IssueId::fromString($command->issueId()), $command->title(), $command->text());
        $this->feedbackForum->add($issue);
    }
}
