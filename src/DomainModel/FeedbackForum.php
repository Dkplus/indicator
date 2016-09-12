<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

interface FeedbackForum
{
    /**
     * @param IssueId $issueId
     * @return Issue
     * @throws IssueNotFound
     */
    public function withId(IssueId $issueId): Issue;
    public function add(Issue $issue);
}
