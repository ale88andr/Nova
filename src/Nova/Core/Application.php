<?php

use Nova\Core\Env;
use Nova\Helpers\Hash;

class Application {

    protected $controller;

    protected $controllerInstance;

    protected $action;

    protected $route;

    protected $params = [];

    /**
     * Application html layout name
     *
     * @var string
     */
    private static $layout;

    /**
     * Controller generated content
     *
     * @var string
     */
    public $content;

    public static $defaultTitle;

    public static function autoload($class)
    {
        $baseDir    = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . DIRECTORY_SEPARATOR;
        $className  = ltrim($class, '\\');
        $fileName   = '';
        if ($nsFileSeparator = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $nsFileSeparator);
            $className = substr($className, $nsFileSeparator + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        $resourcesPath = [
            $baseDir . 'src' . DIRECTORY_SEPARATOR . $fileName,
            $baseDir . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $fileName,
            $baseDir . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $fileName,
        ];

        try {
            $resource = null;
            foreach($resourcesPath as $path){
                if (is_readable($path))
                    $resource = $path;
            }

            if ($resource)
                require_once $resource;
            else
                throw new Exception('File ' . $fileName . ' not exists<br>Search path:<br>' . implode('<br>', $resourcesPath));

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public static function initialize()
    {
        spl_autoload_register(__NAMESPACE__ . "\\Application::autoload");
    }

    function __construct()
    {
        try {
            $this->beforeLoad();
            $this->initializeController($this->parseURL());
        }
        catch(Exception $e) {
            die($e->getMessage());
        }
        if (empty(static::$layout)) {
            static::setLayout();
        }
    }

    /**
     * Controller handler by request params.
     *
     * @param array $url Request string (URI)
     * @return void
     */
    private function initializeController($url)
    {
        $this->getController($url);

        try{
            $this->createInstance(Env::path('controller') . $this->controller . '.php');
        } catch (Exception $e){
            echo 'wrong controller';
        }

        $this->action = (empty($url[0])) ? Hash::get($this->route, 'action') : Hash::extract($url);
        $this->initializeAction($url);
    }

    /**
     * Require controller by request params.
     *
     * @param string $filePath controller file path
     * @throws RequireFileException
     * @return void
     */
    private function createInstance($filePath)
    {
        if (file_exists($filePath)) {
            $this->controllerInstance = new $this->controller($this);
        } else {
            throw new Exception('controller not created');
        }
    }

    /**
     * Set's controller name
     *
     * @param array $url Request string (URI)
     * @return void
     */
    private function getController(&$url)
    {
        $controller = ucfirst(Hash::extract($url));
//        $route = $this->routes->get($controller);
//
//        if (Hash::keyExists($route, 'resource')) {
//            $controller = $route['resource'];
//        }
//
//        if(is_null($controller)) {
//            $route = $this->routes->get('root');
//            $controller = Hash::get($route, 'resource');
//        }

        $this->controller = $controller . 'Controller';
    }

    /**
     * Action handler by request params.
     *
     * @param array $url Part of request URI
     * @throws Exception
     * @return void
     */
    private function initializeAction($url)
    {
//        try {
//            $this->pathNames($this->route);
//            if (Hash::keyExists($this->route, 'only') && !Hash::contains($this->route['only'], $this->action)) {
//                throw new CallActionException(
//                    $this->controllerName,
//                    $this->action,
//                    $this->route['only']
//                );
//            } elseif (method_exists($this->controller, $this->action)) {
//                $this->params = $url;
//                ob_start();
//                call_user_func_array([$this->controller, $this->action], $this->params);
//                $this->content = ob_get_clean();
//            } else {
//                throw new CallActionException(
//                    get_class($this->controller),
//                    $this->action
//                );
//            }
//        } catch(CallActionException $e){
//            ActionController::routingError($e);
//        }
        $this->params = $url;
        call_user_func_array([$this->controllerInstance, $this->action], $this->params);
    }

    private function pathNames()
    {
        if(Hash::keyExists($this->route, 'alias')){
            $names = array_flip($this->route['alias']);
            if(Hash::keyExists($names, $this->action)){
                $this->action = $names[$this->action];
            }
        }
    }

    /**
     * Set application html layout.
     *
     * @param bool|string $layout HTML layout name
     * @return void
     */
    public static function setLayout($layout = false)
    {
        static::$layout = ($layout) ? $layout : 'application';
    }

    /**
     * Get application html layout.
     *
     * @return Srting
     */
    private static function getLayout()
    {
        return static::$layout;
    }

    /**
     * Apply application html layout.
     *
     * @throws Exception
     * @return string
     */
    public function layout()
    {
        $layout = Env::path('view') . 'layouts' . DIRECTORY_SEPARATOR . self::getLayout() . '.html.php';

        if (file_exists($layout)) {
            return $layout;
        } else {
            echo 'layout not found';
        }
    }

    /**
     * Parse request URI
     *
     * @return array
     */
    function parseURL()
    {
        return $url = explode('/', filter_var(trim($_SERVER['REQUEST_URI'], '/') , FILTER_SANITIZE_URL));
    }

    private function beforeLoad()
    {
//        $this->setErrorReporting();
//        $this->removeMagicQuotes();
//        $this->setTimeZone();
//        session_start();
//        static::$defaultTitle = $this->config->get('title');
    }

    public function title()
    {
        return static::$defaultTitle;
    }

    public static function setTitle($title = null, $separator = ' - ')
    {
        if(!is_null($title)){
            static::$defaultTitle .= $separator . $title;
        }
    }
} 