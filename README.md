# laravel-env-overloader
A Dotenv extension to use multiple environment config files.

Environment overloading can be useful when dealing with local environment variables (such as APP_KEY)
or when dealing with secrets that should not be committed to your repository.

## Installation
### Install with composer
```bash
composer require bmaximilian/laravel-env-overloader
```

### Overload the environment with multiple env files
Paste the following code in your `bootstrap/app.php`
```php
$env = $app->detectEnvironment(function() {
    MaximilianBeck\Environment\Overloader\EnvironmentOverloader::overload();
});
```
### Use commands to generate a simple .env file
If you are on Laravel < 5.4: Add the following provider to your `$providers` in `config/app.php`
```php
MaximilianBeck\Environment\Overloader\Console\Providers\EnvironmentConsoleServiceProvider::class,
```
For newer Laravel versions, this service provider should be auto-discovered.

## Usage
Create .env Files in the root directory of your Laravel application.
The `.env.base` File can be created for configuration that should be the same for all environments (e.g. feature flags).
The `.env.${APP_ENV}` File is loaded for all specific configurations for the currently deployed environment.
You can also pass an array of file names to the `EnvironmentOverloader::overload()` as first argument for more custom environment files.
The standard `.env` File is loaded as master which overrides other files.

