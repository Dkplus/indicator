<?php
declare(strict_types=1);
namespace Dkplus\Indicator\ExternalService\GitLab;

use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasReported;
use Gitlab\Client;

class GitLabExporter
{
    /** @var Client */
    private $client;

    /** @var int */
    private $projectId;

    public function __construct(Client $client, int $projectId)
    {
        $this->client = $client;
        $this->projectId = $projectId;
    }

    public function onIssueWasReported(IssueWasReported $event)
    {
        $this->client->issues->create($this->projectId, [
            'title' => $event->title(),
            'description' => sprintf(
                "[%s][Reported by %s]\n\n%s",
                $event->aggregateId(),
                $event->reporterId(),
                $event->text()
            ),
            'labels' => 'public,' . $event->type(),
        ]);
    }

    public function onIssueWasImported(IssueWasImported $event)
    {
        $this->client->issues->addComment(
            $this->projectId,
            $event->externalServiceId(),
            '[' . $event->aggregateId() . ']'
        );
    }
}
