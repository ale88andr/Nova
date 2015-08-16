<?php

namespace Nova\Core;

class ResourceParameters {

    private $params;

    public function __set($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function getParams()
    {
        print_r($this->params);
        return empty($this->params) ? [] : $this->params ;
    }
} 