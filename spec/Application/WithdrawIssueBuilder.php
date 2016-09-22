<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\WithdrawIssue;
use Ramsey\Uuid\Uuid;
use spec\Dkplus\Indicator\Builder;

/**
 * @method WithdrawIssue build()
 */
class WithdrawIssueBuilder extends Builder
{
    public static function aWithdrawnIssueCommand(): self
    {
        return new self(['issueId' => Uuid::uuid4(), 'customerId' => Uuid::uuid4()->toString()], 'withId');
    }
}
