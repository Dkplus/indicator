<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

interface FeedbackForum
{
    public function withId(IssueId $issueId): Issue;
    public function add(Issue $issue);
}
