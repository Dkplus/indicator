<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\Customers;
use Dkplus\Indicator\DomainModel\IssueType;

class ReportIssueHandler
{
    /** @var FeedbackForum */
    private $feedbackForum;

    /** @var Customers */
    private $reporters;

    public function __construct(FeedbackForum $feedbackForum, Customers $reporters)
    {
        $this->feedbackForum = $feedbackForum;
        $this->reporters = $reporters;
    }

    public function __invoke(ReportIssue $command)
    {
        $issueId = IssueId::fromString($command->issueId());
        $type = IssueType::fromString($command->type());

        $issue = $this->reporters
            ->withId(CustomerId::fromString($command->reporterId()))
            ->reportIssue($issueId, $command->title(), $command->text(), $type);

        $this->feedbackForum->add($issue);
    }
}
