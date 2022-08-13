<?php

namespace Brendi211\Currency;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Cache\Factory as FactoryContract;

class Currency
{

    protected $config = [];

    protected $cache;

    protected $user_currency;

    protected $driver;

    protected $formatter;

    protected $currencies_cache;

    public function __construct(array $config, FactoryContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache->store($this->config('cache_driver'));
    }


    public function convert($amount, $from = null, $to = null, $format = true)
    {
        // Get currencies involved
        $from = $from ?: $this->config('default');
        $to = $to ?: $this->getUserCurrency();

        // Get exchange rates
        $from_rate = $this->getCurrencyProp($from, 'exchange_rate');
        $to_rate = $this->getCurrencyProp($to, 'exchange_rate');

        // Skip invalid to currency rates
        if ($to_rate === null) {
            return null;
        }

        try {
            if ($from === $to) {
                $value = $amount;
            } else {
                $value = $amount * $from_rate / $to_rate;
            }
        } catch (\Exception $e) {
            return null;
        }

        // Should the result be formatted?
        if ($format === true) {
            return $this->format($value, $to);
        }

        // Return value
        return $value;
    }

    public function format($value, $code = null, $include_symbol = true): string
    {
        // Get default currency if one is not set
        $code = $code ?: $this->config('default');

        // Remove unnecessary characters
        $value = preg_replace('/[\s\',!]/', '', $value);

        // Check for a custom formatter
        if ($formatter = $this->getFormatter()) {
            return $formatter->format($value, $code);
        }

        // Get the measurement format
        $format = $this->getCurrencyProp($code, 'format');

        // Value Regex
        $valRegex = '/([0-9].*|)[0-9]/';

        // Match decimal and thousand separators
        preg_match_all('/[\s\',.!]/', $format, $separators);

        if ($thousand = Arr::get($separators, '0.0', null)) {
            if ($thousand == '!') {
                $thousand = '';
            }
        }

        $decimal = Arr::get($separators, '0.1', null);

        // Match format for decimals count
        preg_match($valRegex, $format, $valFormat);

        $valFormat = Arr::get($valFormat, 0, 0);

        // Count decimals length
        $decimals = $decimal ? strlen(substr(strrchr($valFormat, $decimal), 1)) : 0;

        // Do we have a negative value?
        if ($negative = $value < 0 ? '-' : '') {
            $value = $value * -1;
        }

        // Format the value
        $value = number_format($value, $decimals, $decimal, $thousand);

        // Apply the formatted measurement
        if ($include_symbol) {
            $value = preg_replace($valRegex, $value, $format);
        }

        // Return value
        return $negative . $value;
    }

    public function setUserCurrency($code)
    {
        $this->user_currency = strtoupper($code);
    }

    public function getUserCurrency()
    {
        return $this->user_currency ?: $this->config('default');
    }

    public function hasCurrency($code)
    {
        return array_key_exists(strtoupper($code), $this->getCurrencies());
    }

    public function isActive($code)
    {
        return $code && (bool) Arr::get($this->getCurrency($code), 'active', false);
    }

    public function getCurrency($code = null)
    {
        $code = $code ?: $this->getUserCurrency();

        return Arr::get($this->getCurrencies(), strtoupper($code));
    }

    public function getCurrencies()
    {
        if ($this->currencies_cache === null) {
            if (config('app.debug', false) === true) {
                $this->currencies_cache = $this->getDriver()->all();
            } else {
                $this->currencies_cache = $this->cache->rememberForever('brendi211.currency', function () {
                    return $this->getDriver()->all();
                });
            }
        }

        return $this->currencies_cache;
    }

    public function getActiveCurrencies()
    {
        return array_filter($this->getCurrencies(), function ($currency) {
            return $currency['active'] == true;
        });
    }

    public function getDriver()
    {
        if ($this->driver === null) {
            // Get driver configuration
            $config = $this->config('drivers.' . $this->config('driver'), []);

            // Get driver class
            $driver = Arr::pull($config, 'class');

            // Create driver instance
            $this->driver = new $driver($config);
        }

        return $this->driver;
    }

    public function getFormatter()
    {
        if ($this->formatter === null && $this->config('formatter') !== null) {
            // Get formatter configuration
            $config = $this->config('formatters.' . $this->config('formatter'), []);

            // Get formatter class
            $class = Arr::pull($config, 'class');

            // Create formatter instance
            $this->formatter = new $class(array_filter($config));
        }

        return $this->formatter;
    }

    public function clearCache(): void
    {
        $this->cache->forget('brendi211.currency');
        $this->currencies_cache = null;
    }

    public function config($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    protected function getCurrencyProp($code, $key, $default = null)
    {
        return Arr::get($this->getCurrency($code), $key, $default);
    }

    public function __get($key)
    {
        return Arr::get($this->getCurrency(), $key);
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getDriver(), $method], $parameters);
    }
}
