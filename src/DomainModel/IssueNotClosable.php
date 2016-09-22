<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Exception;

class IssueNotClosable extends Exception
{
    public static function becauseItHasBeenImported(IssueId $issueId): self
    {
        return new self("Issue $issueId cannot be closed because it has not been reported but imported");
    }

    public static function becauseOnlyTheReportingUserIsAllowedTo(IssueId $issueId, CustomerId $customerId): self
    {
        return new self(
            "Issue $issueId cannot be closed by $customerId because it has been reported by another customer"
        );
    }
}
