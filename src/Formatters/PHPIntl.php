<?php

namespace Brendi211\Currency\Formatters;

use NumberFormatter;
use Brendi211\Currency\Contracts\FormatterInterface;

class PHPIntl implements FormatterInterface
{

    protected $formatter;

    public function __construct()
    {
        $this->formatter = new NumberFormatter(config('app.locale'), NumberFormatter::CURRENCY);
    }

    public function format($value, $code = null)
    {
        return $this->formatter->formatCurrency($value, $code);
    }
}