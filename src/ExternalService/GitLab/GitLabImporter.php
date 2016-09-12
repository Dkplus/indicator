<?php
declare(strict_types=1);
namespace Dkplus\Indicator\ExternalService\GitLab;

use DateTimeImmutable;
use DateTimeZone;
use Dkplus\Indicator\Application\ImportIssue;
use Dkplus\Indicator\Application\RecoverIssue;
use Dkplus\Indicator\DomainModel\IssueId;
use Gitlab\Api\Issues;
use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\Note;
use Ramsey\Uuid\Uuid;

class GitLabImporter
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function importFromHook(array $data): array
    {
        if (! isset($data['object_kind']) || $data['object_kind'] !== 'issue') {
            return [];
        }

        $issue = $this->client->issues->show(
            $data['object_attributes']['project_id'],
            $data['object_attributes']['id']
        ); /* @var $issue \Gitlab\Model\Issue */

        if (! in_array('public', $issue->labels)) {
            // todo: remove
            return [];
        }

        return [
            ImportIssue::fromExternalService(
                Uuid::uuid4(),
                $issue->title,
                $issue->description,
                $this->stateOfIssue($issue),
                $this->typeOfIssue($issue),
                (string) $issue->iid,
                (string) $issue->id
            )
        ];
    }

    public function recoverFromProject(int $projectId): array
    {
        $page = 1;
        $issues = [];
        do {
            $nextPage = $this->client->issues->all($projectId, $page, Issues::PER_PAGE, ['labels' => 'public']);
            $issues = array_merge($issues, $nextPage);
            ++$page;
        } while (count($nextPage) === Issues::PER_PAGE);

        $result = [];
        foreach ($issues as $each) { /* @var $each \Gitlab\Model\Issue */
            $result[] = $this->issueToRecoverCommand($each, $projectId);
        }
        return array_filter($result);
    }

    /**
     * @param Issue $issue
     * @return RecoverIssue|null
     */
    private function issueToRecoverCommand($issue, int $projectId)
    {
        if (! in_array('public', $issue->labels)) {
            return null;
        }

        $issueId = null;
        $customerId = null;
        $text = $issue->description;

        $issueIdRegExp = '/^\[([a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12})\](.*)$/';
        $customerIdRegExp = '/^\[Reported by ([^\]]+)\](.*)$/';
        if (preg_match($issueIdRegExp, $text, $matches)) {
            $issueId = $matches[1];
            $text = preg_replace($issueIdRegExp, '$2', $text);
        }
        if (preg_match($customerIdRegExp, $text, $matches)) {
            $customerId = $matches[1];
            $text = preg_replace($customerIdRegExp, '$2', $text);
        }

        if ($issueId === null) {
            /** @var $eachComment Note */
            foreach ($this->client->issues->showComments($projectId, $issue->id) as $eachComment) {
                if (preg_match($issueIdRegExp, $eachComment->body, $matches)) {
                    $issueId = $matches[1];
                }
            }
        }

        return RecoverIssue::fromExternalService(
            $issueId,
            $issue->title,
            $text,
            $this->stateOfIssue($issue),
            $this->typeOfIssue($issue),
            (string) $issue->iid,
            (string) $issue->id,
            $customerId,
            DateTimeImmutable::createFromFormat(DATE_ISO8601, $issue->created_at, new DateTimeZone('UTC'))
        );
    }

    /**
     * @param Issue $issue
     * @return string
     */
    private function stateOfIssue($issue): string
    {
        if ($issue->state !== 'closed') {
            return 'open';
        }
        return in_array('won\'t fix', $issue->labels) ? 'rejected' : 'implemented';
    }

    /**
     * @param Issue $issue
     * @return string
     */
    private function typeOfIssue($issue): string
    {
        return in_array('bug', $issue->labels)
            ? 'bug'
            : 'enhancement';
    }
}