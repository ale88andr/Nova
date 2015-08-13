<?php

class Application {
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
                if (file_exists($path)){
                    $resource = $path;
                    break;
                }
            }

            if ($resource)
                require_once $resource;
            else
                throw new Exception('File ' . $fileName . ' not exists<br>Search path:<br>' . implode('<br>', $resourcesPath));
            var_dump($resource);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public static function initialize()
    {
        spl_autoload_register(__NAMESPACE__ . "\\Application::autoload");
    }
} 