<?php
/**
 * Created on 2019-09-18.
 *
 * @author Maximilian Beck <contact@maximilianbeck.de>
 */

namespace MaximilianBeck\Environment\Overloader\Console\Commands;

use Illuminate\Foundation\Application;
use Illuminate\Console\Command;

class EnvironmentSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'environment:setup
                                {--show : Display the key instead of modifying files}
                                {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a .env file and generate a key for it.';

    /**
     * @var Application
     */
    private $app;

    /**
     * Create a new command instance.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct();
        $this->output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $this->app = $app;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Read out the current app env file to reset it later
        $envFile = $this->app->environmentFile();

        // Generate the key in the .env
        $this->info('Setting application key in .env');
        $this->app->loadEnvironmentFrom('.env');

        $this->call('key:generate');

        // Reset env file to the detected one
        $this->info('Using ' . $envFile . ' as default environment file.');
        $this->app->loadEnvironmentFrom($envFile);
    }
}
