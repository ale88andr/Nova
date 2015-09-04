<?php

namespace Nova\Core;

use Nova\Core\Exceptions\LogicExceptions\ArgumentError;
use Nova\Core\Exceptions\LogicExceptions\RequireFileException;
use Nova\Helpers\Hash;

class Viewer {

    private $viewsPath;

    public function __construct()
    {
        $this->viewsPath = implode(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'app', 'views', '']);
    }

    public function render($partial, $variables = [])
    {
        try {
            if (is_array($variables)){
                foreach ($variables as $variable => $value){
                    $ {
                    $variable
                    } = $value;
                }

                unset($variables);
            } else {
                throw new ArgumentError();
            }

            $partialSubPath = $this->getPartialPath($partial);
            $partialPath = $this->viewsPath . Hash::get($partialSubPath, 'dir') . DIRECTORY_SEPARATOR . Hash::get($partialSubPath, 'file');

            if (file_exists($partialPath)) {
                ob_start();
                require_once $partialPath;
                return $content = ob_get_clean();
            } else {
                throw new RequireFileException($partialPath);
            }
        } catch (ArgumentError $e) {
            die($e->printTrace());
        } catch (RequireFileException $e){
            die($e->printTrace());
        }
    }

    private function getPartialPath($partial)
    {
        $path = [];
        $partial = ltrim($partial, '/') . '.html.php';
        if (!strpos($partial, '/')) {
            Hash::set($path, 'file', $partial);
        } else {
            $tmp = explode('/', $partial);
            Hash::set($path, 'file', array_pop($tmp));
            Hash::set($path, 'dir', join($tmp));
        }

        return $path;
    }

} 