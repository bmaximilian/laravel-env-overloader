<?php

namespace Tests;

use MaximilianBeck\Environment\Overloader\Console\Providers\EnvironmentOverloaderConsoleServiceProvider;
use MaximilianBeck\Environment\Overloader\EnvironmentOverloader;
use Test\Mock\Http\Kernel;

abstract class AbstractTestCase extends \Orchestra\Testbench\TestCase {
    protected function getPackageProviders($app) {
        return [
            EnvironmentOverloaderConsoleServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        EnvironmentOverloader::overload();
    }
}
