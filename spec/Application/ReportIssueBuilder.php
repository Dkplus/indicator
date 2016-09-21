<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\ReportIssue;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use spec\Dkplus\Indicator\Builder;

/**
 * @method ReportIssue build()
 */
class ReportIssueBuilder extends Builder
{
    public static function aReportIssueCommand(): self
    {
        $faker = Factory::create();
        return new self([
            'customerId' => $faker->uuid,
            'issueId' => Uuid::uuid4(),
            'title' => $faker->sentence,
            'text' => $faker->text,
            'type' => 'bug',
        ], 'withTitleAndText');
    }
}
