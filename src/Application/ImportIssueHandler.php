<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Dkplus\Indicator\DomainModel\FeedbackForum;
use Dkplus\Indicator\DomainModel\Issue;
use Dkplus\Indicator\DomainModel\IssueId;
use Dkplus\Indicator\DomainModel\IssueNotFound;
use Dkplus\Indicator\DomainModel\IssueState;
use Dkplus\Indicator\DomainModel\IssueType;

class ImportIssueHandler
{
    /** @var FeedbackForum */
    private $feedbackForum;

    public function __construct(FeedbackForum $feedbackForum)
    {
        $this->feedbackForum = $feedbackForum;
    }

    public function __invoke(ImportIssue $command)
    {
        $issueId = IssueId::fromString($command->issueId());
        $state = IssueState::fromString($command->state());
        $type = IssueType::fromString($command->type());

        try {
            $this->feedbackForum
                ->withId($issueId)
                ->importFromExternalService(
                    $command->title(),
                    $command->text(),
                    $command->issueNumber(),
                    $command->externalServiceId(),
                    $state,
                    $type
                );
        } catch (IssueNotFound $exception) {
            $issue = Issue::importFromAnExternalService(
                $issueId,
                $command->title(),
                $command->text(),
                $command->issueNumber(),
                $command->externalServiceId(),
                $state,
                $type
            );
            $this->feedbackForum->add($issue);
        }
    }
}
