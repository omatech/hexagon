<?php

namespace Omatech\Hexagon\Tests;

use Orchestra\Testbench\TestCase;
use Omatech\Hexagon\HexagonServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [HexagonServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
