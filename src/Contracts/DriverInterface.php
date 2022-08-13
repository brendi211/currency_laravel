<?php

namespace Brendi211\Currency\Contracts;

use DateTime;

interface DriverInterface
{

    public function create(array $params);

    public function all();

    public function find($code, $active = 1);

    public function update($code, array $attributes, DateTime $timestamp = null);

    public function delete($code);
}