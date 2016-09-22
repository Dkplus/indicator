<?php
declare(strict_types=1);
namespace Dkplus\Indicator\ExternalService\GitLab;

use Dkplus\Indicator\DomainModel\Event\IssueWasClosed;
use Dkplus\Indicator\DomainModel\Event\IssueWasImplemented;
use Dkplus\Indicator\DomainModel\Event\IssueWasImported;
use Dkplus\Indicator\DomainModel\Event\IssueWasRejected;
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


    public function onIssueWasClosed(IssueWasClosed $event)
    {
        if (! $event->externalServiceId()) {
            // it may happen that an issue is reported and closed
            // before it has been exported to gitlab ci and reimported to the indicator
            // but this may happen rarely so for now we won't handle this.
            return;
        }
        /* @var $issue \Gitlab\Model\Issue */
        $issue = $this->client->issues->show($this->projectId, $event->externalServiceId());
        $this->client->issues->update($this->projectId, $event->externalServiceId(), [
            'state_event' => 'close',
            'labels' => implode(',', array_merge($issue->labels, ['closed']))
        ]);
    }
}
