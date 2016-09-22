<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\CloseIssue;
use Ramsey\Uuid\Uuid;
use spec\Dkplus\Indicator\Builder;

/**
 * @method CloseIssue build()
 */
class CloseIssueBuilder extends Builder
{
    public static function aCloseIssueCommand(): self
    {
        return new self(['issueId' => Uuid::uuid4(), 'customerId' => Uuid::uuid4()->toString()], 'withId');
    }
}
