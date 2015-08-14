<?php

namespace Nova\Interfaces;

interface AccessInterface
{
    public function get($key);

    public function set($key, $value);

    public function keyExists($key);
}