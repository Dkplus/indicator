<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;

class CloseIssue extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withId(Uuid $issueId, string $customerId)
    {
        return new self([
            'issueId' => $issueId->toString(),
            'customerId' => $customerId
        ]);
    }

    public function issueId(): string
    {
        return $this->payload()['issueId'];
    }

    public function customerId(): string
    {
        return $this->payload()['customerId'];
    }
}
