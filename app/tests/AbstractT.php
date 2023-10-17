<?php

declare(strict_types = 1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

abstract class AbstractT extends ApiTestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        echo "Constructor" . PHP_EOL;
    }
}