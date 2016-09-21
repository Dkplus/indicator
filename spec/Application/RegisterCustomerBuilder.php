<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator\Application;

use Dkplus\Indicator\Application\RegisterCustomer;
use Faker\Factory;
use spec\Dkplus\Indicator\Builder;

/**
 * @method RegisterCustomer build()
 */
class RegisterCustomerBuilder extends Builder
{
    public static function aRegisterCustomerCommand()
    {
        $faker = Factory::create();
        return new self([
            'customerId' => $faker->uuid,
            'name' => $faker->name,
        ], 'withName');
    }
}
