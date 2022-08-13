<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Currency
    |--------------------------------------------------------------------------
    |
    | The application currency determines the default currency that will be
    | used by the currency service provider. You are free to set this value
    | to any of the currencies which will be supported by the application.
    |
    */

    'default' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | API Key for OpenExchangeRates.org
    |--------------------------------------------------------------------------
    |
    | Only required if you with to use the Open Exchange Rates api. You can
    | always just use Yahoo, the current default.
    |
    */

    'api_key' => '',

    /*
    |--------------------------------------------------------------------------
    | Default Storage Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default storage driver that should be used
    | by the framework.
    |
    | Supported: "database", "filesystem"
    |
    */

    'driver' => 'database',

    'cache_driver' => null,

    'drivers' => [

        'database' => [
            'class' => \Brendi211\Currency\Drivers\Database::class,
            'connection' => null,
            'table' => 'currencies',
        ],

        'filesystem' => [
            'class' => \Brendi211\Currency\Drivers\Filesystem::class,
            'disk' => null,
            'path' => 'currencies.json',
        ],

    ],

    'formatter' => null,

    'formatters' => [

        'php_intl' => [
            'class' => \Brendi211\Currency\Formatters\PHPIntl::class,
        ],

    ],
];