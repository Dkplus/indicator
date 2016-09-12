<?php
declare(strict_types=1);
namespace Dkplus\Indicator\DomainModel;

use Exception;

class IssueNotFound extends Exception 
{
    public static function withId($id)
    {
        return new self("Could not found an issue with id $id");
    }
}
