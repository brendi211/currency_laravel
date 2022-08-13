<?php


    function currency($amount = null, $from = null, $to = null, $format = true)
    {
        if (is_null($amount)) {
            return app('currency');
        }
        return app('currency')->convert($amount, $from, $to, $format);
    }


if (! function_exists('currency_format')) {

    function currency_format($amount = null, $currency = null, $include_symbol = true): string
    {
        return app('currency')->format($amount, $currency, $include_symbol);
    }
}
