<?php

namespace Brendi211\Currency\Contracts;

interface FormatterInterface
{

    public function format($value, $code = null);
}