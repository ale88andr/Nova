<?php

namespace Nova\Core;

use Nova\Helpers\Hash;

class BaseController {

    protected $view;

    private $params;

    public function __construct()
    {
        $this->view = new Viewer();
    }

    protected function render($partial = null)
    {
        if (is_null($partial)){
            $subDir = str_replace('Controller', '', get_class($this));
            $partial = strtolower($subDir) . DIRECTORY_SEPARATOR . $this->getCallingMethodName();
        }

        $this->view->render($partial, $this->getParams());
    }

    private function getCallingMethodName()
    {
        return Hash::get(debug_backtrace(), '2.function');
    }

    public function __set($key, $value)
    {
        Hash::set($this->params, $key, $value);
    }

    public function getParams()
    {
        return empty($this->params) ? [] : $this->params ;
    }
} 