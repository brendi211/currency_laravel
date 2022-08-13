<?php

namespace Brendi211\Currency\Middleware;

use Closure;
use Illuminate\Http\Request;

class CurrencyMiddleware
{

    public function handle($request, Closure $next)
    {
        // Don't redirect the console
        if ($this->runningInConsole()) {
            return $next($request);
        }

        // Check for a user defined currency
        if (($currency = $this->getUserCurrency($request)) === null) {
            $currency = $this->getDefaultCurrency();
        }

        // Set user currency
        $this->setUserCurrency($currency, $request);

        return $next($request);
    }

    protected function getUserCurrency(Request $request)
    {
        // Check request for currency
        $currency = $request->get('currency');
        if ($currency && currency()->isActive($currency) === true) {
            return $currency;
        }

        // Get currency from session
        $currency = $request->session()->get('currency');
        if ($currency && currency()->isActive($currency) === true) {
            return $currency;
        }

        return null;
    }

    protected function getDefaultCurrency()
    {
        return currency()->config('default');
    }

    private function runningInConsole()
    {
        return app()->runningInConsole();
    }

    private function setUserCurrency($currency, $request): string
    {
        $currency = strtoupper($currency);

        // Set user selection globally
        currency()->setUserCurrency($currency);

        // Save it for later too!
        $request->session()->put(['currency' => $currency]);

        return $currency;
    }
}
