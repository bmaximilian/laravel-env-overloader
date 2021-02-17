<?php
/**
 * Created on 2019-09-18.
 *
 * @author Maximilian Beck <contact@maximilianbeck.de>
 */

namespace MaximilianBeck\Environment\Overloader\Console\Providers;

use Illuminate\Support\ServiceProvider;
use MaximilianBeck\Environment\Overloader\Console\Commands\EnvironmentSetup;

/**
 * Class EnvironmentOverloaderConsoleServiceProvider
 *
 * @package MaximilianBeck\Environment\Overloader\Console\Providers
 */
class EnvironmentOverloaderConsoleServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->commands([
            EnvironmentSetup::class,
        ]);
    }
}

