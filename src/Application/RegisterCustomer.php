<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Application;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RegisterCustomer extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withName(string $customerId, string $name)
    {
        return new self(['customerId' => $customerId, 'name' => $name]);
    }

    public function customerId(): string
    {
        return $this->payload()['customerId'];
    }

    public function name(): string
    {
        return $this->payload()['name'];
    }
}
