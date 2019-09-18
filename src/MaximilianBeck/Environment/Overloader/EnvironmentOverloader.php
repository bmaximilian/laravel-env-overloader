<?php

namespace MaximilianBeck\Environment\Overloader;

use Dotenv\Dotenv;

class EnvironmentOverloader {
    /**
     * Overload the environment variables with an array of .env files
     * relative to the application base path.
     * Doubled variables will be overridden from top to bottom (last element in file array wins)
     *
     * @param string[] $dotEnvFiles
     */
    static function overload(array $dotEnvFiles = ['.env.secrets',]) {
        $appEnv = env('APP_ENV');
        $appEnvFileName = '.env.' . $appEnv;

        // Add .env.base as file to share env entries across different app_env's
        $staticEnvFiles = [env('APP_BASE_ENV_FILE_NAME', '.env.base')];

        // Add .env.APP_ENV file with specific configurations dedicated to the APP_ENV of the server (if APP_ENV is set)
        if ($appEnv) {
            $staticEnvFiles[] = $appEnvFileName;
        }

        // Merges the basic/static env files with the custom ones from the parameter
        // Add .env as master env file to override all
        $allEnvFiles = array_merge($staticEnvFiles, $dotEnvFiles, ['.env',]);

        // overload the environment with all collected files
        collect($allEnvFiles)->each(static function ($dotEnvFile) {
            if (file_exists(base_path($dotEnvFile))) {
                Dotenv::create(base_path(), $dotEnvFile)->overload();
            }
        });
    }
}

