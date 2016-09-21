<?php
declare(strict_types=1);
namespace spec\Dkplus\Indicator;

use Dkplus\Formica\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    public function __construct(array $arguments, string $staticFactoryMethod = null, string $class = null)
    {
        if ($class === null) {
            $class = substr(get_class($this), 5, -7);
        }
        parent::__construct($arguments, $staticFactoryMethod, $class);
    }
}
