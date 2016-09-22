<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueNotClosable;

class CloseIssueHandler
{
    /** @var FeedbackForum */
    private $feedbackForum;

    public function __construct(FeedbackForum $feedbackForum)
    {
        $this->feedbackForum = $feedbackForum;
    }

    public function __invoke(CloseIssue $data)
    {
        $issueId = IssueId::fromString($data->issueId());
        $customerId = CustomerId::fromString($data->customerId());

        $issue = $this->feedbackForum->withId($issueId);
        if (! $issue->reporterId()->equals($customerId)) {
            throw IssueNotClosable::becauseOnlyTheReportingUserIsAllowedTo($issueId, $customerId);
        }
        $issue->close();
    }
}
