<?php

namespace Brendi211\Currency\Facades;

use Illuminate\Support\Facades\Facade;

class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'currency';
    }
}