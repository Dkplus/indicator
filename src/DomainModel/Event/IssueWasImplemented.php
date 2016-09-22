<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel\Event;

use Dkplus\Indicator\DomainModel\IssueId;
use Prooph\EventSourcing\AggregateChanged;

class IssueWasImplemented extends AggregateChanged
{
    public static function bySystem(IssueId $issueId, string $externalServiceId): self
    {
        return self::occur((string) $issueId, ['externalServiceId' => $externalServiceId]);
    }

    public function externalServiceId(): string
    {
        return $this->payload()['externalServiceId'];
    }
}
