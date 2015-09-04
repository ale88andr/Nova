<?php

use Nova\Core\Env;
use Nova\Core\Router;
use Nova\Core\Exceptions\LogicExceptions\CallActionException;
use Nova\Helpers\Hash;

class Application {

    protected $controller;

    protected $controllerInstance;

    protected $action;

    protected $route;

    protected $routes;

    protected $params = [];

    protected $env;

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
            $this->initializeRoutes($this->parseURL());
            $this->initializeController($this->parseURL());
        }
        catch(Exception $e) {
            die($e->getMessage());
        }
        if (empty(static::$layout)) {
            static::setLayout();
        }
    }

    public function __call($name, $value)
    {
        throw new Exception('Unknown Controller/Action requested');
    }

    /**
     * Controller handler by request params.
     *
     * @param array $url Request string (URI)
     * @return void
     */
    private function initializeController($url)
    {
        if(is_readable(Env::path() . implode('/', $url))){
            exit;
        }

        $this->controller = $this->routes->getControllerName();

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
     * Action handler by request params.
     *
     * @param array $url Part of request URI
     * @throws Exception
     * @return void
     */
    private function initializeAction($url)
    {
        try {
            $this->action = $this->routes->getActionName();

            if (method_exists($this->controllerInstance, $this->action)) {
                ob_start();
                call_user_func_array([$this->controllerInstance, $this->action], $this->routes->getParameters());
                $this->content = ob_get_clean();
            } else {
                throw new CallActionException(get_class($this->controllerInstance), $this->action);
            }
        } catch(CallActionException $e){
            die($e->getMessage());
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

    private function initializeRoutes($uri)
    {
        $this->routes = new Router($uri);
    }

    private function beforeLoad()
    {
        $this->env = new Env();
        $this->setErrorReporting();
        $this->removeMagicQuotes();
        $this->setTimeZone();
        session_start();
        static::$defaultTitle = $this->env->get('title');
    }

    private function setErrorReporting()
    {
        error_reporting(E_ALL);
        if ($this->env->get('defaults.environment') == 'development') {
            ini_set('display_errors', 'On');
        }
        else {
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', implode( DIRECTORY_SEPARATOR, [Env::path(), 'tmp', 'logs', 'error.log']));
        }
    }

    private function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
        return ($value);
    }

    private function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = $this->stripSlashesDeep($_GET);
            $_POST = $this->stripSlashesDeep($_POST);
            $_COOKIE = $this->stripSlashesDeep($_COOKIE);
        }
    }

    private function setTimeZone()
    {
        $timezone = $this->env->get('defaults.timezone');
        if(is_string($timezone)){
            date_default_timezone_set($timezone);
        }
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