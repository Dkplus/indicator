<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\ImportIssue;
use Ramsey\Uuid\Uuid;
use spec\Dkplus\Indicator\Builder;

/**
 * @method ImportIssue build()
 */
class ImportIssueBuilder extends Builder
{
    public static function anImportIssueCommand()
    {
        return new self([
            'id' => Uuid::uuid4(),
            'title' => 'Some title',
            'text' => 'Some text',
            'state' => 'open',
            'type' => 'bug',
            'issueNumber' => '4',
            'externalServiceId' => '2',
        ], 'fromExternalService');
    }
}
