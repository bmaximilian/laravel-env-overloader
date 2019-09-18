<?php

namespace Tests\Unit;

use MaximilianBeck\Environment\Overloader\EnvironmentOverloader;
use Tests\AbstractTestCase;
use Tests\CreatesEnvFile;

class EnvironmentOverloaderTest extends AbstractTestCase {
    use CreatesEnvFile;

    public function testThatItOverloadsEnv() {
        $envFile = 'myEnv.env';
        $this->createEnvFile($envFile, [
            'IM_A_TEST_KEY' => 'Hello!',
        ]);

        EnvironmentOverloader::overload([$envFile]);

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertEquals('Hello!', env('IM_A_TEST_KEY'));
        $this->assertEquals('Hello!', getenv('IM_A_TEST_KEY'));

        $this->removeEnvFile($envFile);
    }

    public function testThatItLoadsAppEnvFile() {
        $this->assertEquals('testing', env('APP_ENV'));
        $envFile = '.env.testing';
        $this->createEnvFile($envFile, [
            'IM_A_TEST_KEY' => 'Hello!',
            'IM_THE_SECOND_TEST_KEY' => 'Foo!',
        ]);

        EnvironmentOverloader::overload();

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_THE_SECOND_TEST_KEY', $_ENV);
        $this->assertEquals('Hello!', env('IM_A_TEST_KEY'));
        $this->assertEquals('Foo!', env('IM_THE_SECOND_TEST_KEY'));

        $this->removeEnvFile($envFile);
    }

    public function testThatItLoadsBaseEnvFile() {
        $this->assertEquals(null, env('APP_BASE_ENV_FILE_NAME'));
        $envFile = '.env.base';
        $this->createEnvFile($envFile, [
            'IM_A_TEST_KEY' => 'Hello!',
            'IM_THE_SECOND_TEST_KEY' => 'Foo!',
        ]);

        EnvironmentOverloader::overload();

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_THE_SECOND_TEST_KEY', $_ENV);
        $this->assertEquals('Hello!', env('IM_A_TEST_KEY'));
        $this->assertEquals('Foo!', env('IM_THE_SECOND_TEST_KEY'));

        $this->removeEnvFile($envFile);
    }

    public function testThatItLoadsDotEnvFile() {
        $envFile = '.env';
        $this->createEnvFile($envFile, [
            'IM_A_TEST_KEY' => 'Hello!',
            'IM_THE_SECOND_TEST_KEY' => 'Foo!',
        ]);

        EnvironmentOverloader::overload();

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_THE_SECOND_TEST_KEY', $_ENV);
        $this->assertEquals('Hello!', env('IM_A_TEST_KEY'));
        $this->assertEquals('Foo!', env('IM_THE_SECOND_TEST_KEY'));

        $this->removeEnvFile($envFile);
    }

    public function testThatItWontCrashWhenEnvFileDontExists() {
        $envFile = '.env.notexisting';
        $this->removeEnvFile($envFile);
        $this->expectNotToPerformAssertions();

        EnvironmentOverloader::overload();
    }

    public function testThatItOverridesFiles() {
        $this->assertEquals('testing', env('APP_ENV'));
        $this->assertEquals(null, env('APP_BASE_ENV_FILE_NAME'));

        $this->createEnvFile('.env.base', [
            'IM_LOADED_BY_BASE' => 'base',
            'IM_A_TEST_KEY' => 'test_base',
            'IM_A_TEST_KEY_2' => 'test_2_base',
            'IM_A_TEST_KEY_3' => 'test_3_base',
        ]);
        $this->createEnvFile('.env.testing', [
            'IM_LOADED_BY_TESTING' => 'testing',
            'IM_A_TEST_KEY' => 'test_testing',
            'IM_A_TEST_KEY_2' => 'test_2_testing',
        ]);
        $this->createEnvFile('.env', [
            'IM_LOADED_BY_DOTENV' => 'dotenv',
            'IM_A_TEST_KEY' => 'test_dotenv',
        ]);

        EnvironmentOverloader::overload();

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_2', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_3', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_BASE', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_TESTING', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_DOTENV', $_ENV);

        $this->assertEquals('test_dotenv', env('IM_A_TEST_KEY'));
        $this->assertEquals('test_2_testing', env('IM_A_TEST_KEY_2'));
        $this->assertEquals('test_3_base', env('IM_A_TEST_KEY_3'));
        $this->assertEquals('base', env('IM_LOADED_BY_BASE'));
        $this->assertEquals('testing', env('IM_LOADED_BY_TESTING'));
        $this->assertEquals('dotenv', env('IM_LOADED_BY_DOTENV'));

        $this->removeEnvFile('.env');
        $this->removeEnvFile('.env.testing');
        $this->removeEnvFile('.env.base');
    }

    public function testThatItOverridesFilesWithEnvSecretsByDefault() {
        $this->assertEquals('testing', env('APP_ENV'));
        $this->assertEquals(null, env('APP_BASE_ENV_FILE_NAME'));

        $this->createEnvFile('.env.base', [
            'IM_LOADED_BY_BASE' => 'base',
            'IM_A_TEST_KEY' => 'test_base',
            'IM_A_TEST_KEY_2' => 'test_2_base',
            'IM_A_TEST_KEY_3' => 'test_3_base',
            'IM_A_TEST_KEY_4' => 'test_4_base',
        ]);
        $this->createEnvFile('.env.testing', [
            'IM_LOADED_BY_TESTING' => 'testing',
            'IM_A_TEST_KEY' => 'test_testing',
            'IM_A_TEST_KEY_2' => 'test_2_testing',
            'IM_A_TEST_KEY_3' => 'test_3_testing',
        ]);
        $this->createEnvFile('.env.secrets', [
            'IM_LOADED_BY_SECRETS' => 'secrets',
            'IM_A_TEST_KEY' => 'test_secrets',
            'IM_A_TEST_KEY_2' => 'test_2_secrets',
        ]);
        $this->createEnvFile('.env', [
            'IM_LOADED_BY_DOTENV' => 'dotenv',
            'IM_A_TEST_KEY' => 'test_dotenv',
        ]);

        EnvironmentOverloader::overload();

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_2', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_3', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_4', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_BASE', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_TESTING', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_SECRETS', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_DOTENV', $_ENV);

        $this->assertEquals('test_dotenv', env('IM_A_TEST_KEY'));
        $this->assertEquals('test_2_secrets', env('IM_A_TEST_KEY_2'));
        $this->assertEquals('test_3_testing', env('IM_A_TEST_KEY_3'));
        $this->assertEquals('test_4_base', env('IM_A_TEST_KEY_4'));
        $this->assertEquals('base', env('IM_LOADED_BY_BASE'));
        $this->assertEquals('testing', env('IM_LOADED_BY_TESTING'));
        $this->assertEquals('secrets', env('IM_LOADED_BY_SECRETS'));
        $this->assertEquals('dotenv', env('IM_LOADED_BY_DOTENV'));

        $this->removeEnvFile('.env');
        $this->removeEnvFile('.env.testing');
        $this->removeEnvFile('.env.base');
        $this->removeEnvFile('.env.secrets');
    }

    public function testThatItOverridesFilesWithSortedArrayArgument() {
        $this->assertEquals('testing', env('APP_ENV'));
        $this->assertEquals(null, env('APP_BASE_ENV_FILE_NAME'));

        $this->createEnvFile('.env.base', [
            'IM_LOADED_BY_BASE' => 'base',
            'IM_A_TEST_KEY' => 'test_base',
            'IM_A_TEST_KEY_2' => 'test_2_base',
            'IM_A_TEST_KEY_3' => 'test_3_base',
            'IM_A_TEST_KEY_4' => 'test_4_base',
            'IM_A_TEST_KEY_5' => 'test_5_base',
            'IM_A_TEST_KEY_6' => 'test_6_base',
        ]);
        $this->createEnvFile('.env.testing', [
            'IM_LOADED_BY_TESTING' => 'testing',
            'IM_A_TEST_KEY' => 'test_testing',
            'IM_A_TEST_KEY_2' => 'test_2_testing',
            'IM_A_TEST_KEY_3' => 'test_3_testing',
            'IM_A_TEST_KEY_4' => 'test_4_testing',
            'IM_A_TEST_KEY_5' => 'test_5_testing',
        ]);
        $this->createEnvFile('.env.secrets', [
            'IM_LOADED_BY_SECRETS' => 'secrets',
            'IM_A_TEST_KEY' => 'test_secrets',
            'IM_A_TEST_KEY_2' => 'test_2_secrets',
            'IM_A_TEST_KEY_3' => 'test_3_secrets',
            'IM_A_TEST_KEY_4' => 'test_4_secrets',
        ]);
        $this->createEnvFile('.env.other', [
            'IM_LOADED_BY_OTHER' => 'other',
            'IM_A_TEST_KEY' => 'test_other',
            'IM_A_TEST_KEY_2' => 'test_2_other',
            'IM_A_TEST_KEY_3' => 'test_3_other',
        ]);
        $this->createEnvFile('.env.other2', [
            'IM_LOADED_BY_OTHER2' => 'other2',
            'IM_A_TEST_KEY' => 'test_other2',
            'IM_A_TEST_KEY_2' => 'test_2_other2',
        ]);
        $this->createEnvFile('.env', [
            'IM_LOADED_BY_DOTENV' => 'dotenv',
            'IM_A_TEST_KEY' => 'test_dotenv',
        ]);

        EnvironmentOverloader::overload([
            '.env.secrets',
            '.env.other',
            '.env.other2',
        ]);

        $this->assertArrayHasKey('IM_A_TEST_KEY', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_2', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_3', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_4', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_5', $_ENV);
        $this->assertArrayHasKey('IM_A_TEST_KEY_6', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_BASE', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_TESTING', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_SECRETS', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_OTHER', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_OTHER2', $_ENV);
        $this->assertArrayHasKey('IM_LOADED_BY_DOTENV', $_ENV);

        $this->assertEquals('test_dotenv', env('IM_A_TEST_KEY'));
        $this->assertEquals('test_2_other2', env('IM_A_TEST_KEY_2'));
        $this->assertEquals('test_3_other', env('IM_A_TEST_KEY_3'));
        $this->assertEquals('test_4_secrets', env('IM_A_TEST_KEY_4'));
        $this->assertEquals('test_5_testing', env('IM_A_TEST_KEY_5'));
        $this->assertEquals('test_6_base', env('IM_A_TEST_KEY_6'));
        $this->assertEquals('base', env('IM_LOADED_BY_BASE'));
        $this->assertEquals('testing', env('IM_LOADED_BY_TESTING'));
        $this->assertEquals('secrets', env('IM_LOADED_BY_SECRETS'));
        $this->assertEquals('other', env('IM_LOADED_BY_OTHER'));
        $this->assertEquals('other2', env('IM_LOADED_BY_OTHER2'));
        $this->assertEquals('dotenv', env('IM_LOADED_BY_DOTENV'));

        $this->removeEnvFile('.env');
        $this->removeEnvFile('.env.testing');
        $this->removeEnvFile('.env.base');
        $this->removeEnvFile('.env.secrets');
        $this->removeEnvFile('.env.other');
        $this->removeEnvFile('.env.other2');
    }
}
