<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\CustomerId;
use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueState;
use Dkplus\Indicator\DomainModel\IssueType;

class RecoverIssueHandler
{
    /** @var FeedbackForum */
    private $feedbackForum;

    public function __construct(FeedbackForum $feedbackForum)
    {
        $this->feedbackForum = $feedbackForum;
    }

    public function __invoke(RecoverIssue $command)
    {
        $issueId = IssueId::fromString($command->issueId());
        $state = IssueState::fromString($command->state());
        $type = IssueType::fromString($command->type());
        $reporterId = $command->reporterId() ? CustomerId::fromString($command->reporterId()) : null;

        $issue = Issue::recoverFromExternalService(
            $issueId,
            $command->title(),
            $command->text(),
            $command->issueNumber(),
            $command->externalServiceId(),
            $state,
            $type,
            $command->originallyCreatedAt(),
            $reporterId
        );
        $this->feedbackForum->add($issue);
    }
}
