<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

interface Backlog
{
    public function get(IssueId $issueId);
    public function add(Issue $issue);
}
