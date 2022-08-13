<?php

namespace Brendi211\Currency\Drivers;

use Illuminate\Support\Arr;
use Brendi211\Currency\Contracts\DriverInterface;

abstract class AbstractDriver implements DriverInterface
{

    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    protected function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}