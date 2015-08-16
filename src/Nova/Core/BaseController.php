<?php

namespace Nova\Core;

use Nova\Helpers\Hash;

/**
 * Class BaseController
 * @package Nova\Core
 */
class BaseController {

    /**
     * create an instance of Viewer class
     * @var Viewer
     */
    protected $view;

    /**
     * magic params from child controller
     * @var
     */
    private $params = [];

    public function __construct()
    {
        $this->view = new Viewer();
    }

    /**
     * Render a template file or Controller/method file name
     * @param string|null $partial
     */
    protected function render($partial = null)
    {
        if (is_null($partial)){
            $subDir = str_replace('Controller', '', get_class($this));
            $partial = strtolower($subDir) . DIRECTORY_SEPARATOR . $this->getCallingMethodName();
        }

        echo $this->view->render($partial, $this->params);
    }

    /**
     * return method caller from backtrace
     * @return string
     */
    private function getCallingMethodName()
    {
        return Hash::get(debug_backtrace(), '2.function');
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        Hash::set($this->params, $key, $value);
    }
} 