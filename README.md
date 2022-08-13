Composer
From the command line run:

    composer require brendi211/currency

Manual Setup
Once installed you need to register the service provider with the application. Open up config/app.php and find the providers key.

    'providers' => [
        \Brendi211\Currency\CurrencyServiceProvider::class,
    ]
This package also comes with a facade, which provides an easy way to call the the class. Open up config/app.php and find the aliases key.

    'aliases' => [
        'Currency' => \Brendi211\Currency\Facades\Currency::class,
    ];


# next

    php artisan vendor:publish --provider="Brendi211\Currency\CurrencyServiceProvider" --tag=config

A configuration file will be published to config/currency.php.

# Migration

    php artisan vendor:publish --provider="Brendi211\Currency\CurrencyServiceProvider" --tag=migrations

Run this on the command line from the root of your project to generate the table for storing currencies:

    php artisan migrate

# Middleware

    protected $middleware = [
        \Illuminate\Session\Middleware\StartSession::class
        \Brendi211\Currency\Middleware\CurrencyMiddleware::class,

    ]