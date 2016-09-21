<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\RecoverIssue;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use spec\Dkplus\Indicator\Builder;

/**
 * @method RecoverIssue build()
 */
class RecoverIssueBuilder extends Builder
{
    public static function aRecoverIssueCommand()
    {
        $faker = Factory::create();
        return new self([
            'issueId' => Uuid::uuid4(),
            'title' => $faker->sentence,
            'text' => $faker->text,
            'state' => 'open',
            'type' => 'bug',
            'issueNumber' => (string) $faker->randomDigit,
            'externalServiceId' => (string) $faker->randomDigit,
            'customerId' => null,
            'originallyCreatedAt' => $faker->dateTimeThisMonth,
        ], 'fromExternalService');
    }

    public function withAnyCustomerId(): self
    {
        $this->setArgument('customerId', (string) Uuid::uuid4());
        return $this;
    }
}
