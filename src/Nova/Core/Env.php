<?php

namespace Nova\Core;

use Symfony\Component\Yaml\Yaml;
use Nova\Helpers\Hash;
use Nova\Interfaces\AccessInterface;

class Env implements AccessInterface {

    private $yaml;

    public function __construct($ymlFile = null)
    {
        $ymlFile = is_null($ymlFile) ? self::path('config') . 'settings.yml' : $ymlFile;
        $this->yaml = Yaml::parse(file_get_contents($ymlFile));
    }

    public static function path($resource = null)
    {
        $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        switch ($resource) {
            case 'controller':
                return implode(DIRECTORY_SEPARATOR, [$root, 'app', 'controllers', '']);
                break;
            case 'model':
                return implode(DIRECTORY_SEPARATOR, [$root, 'app', 'models', '']);
                break;
            case 'view':
                return implode(DIRECTORY_SEPARATOR, [$root, 'app', 'views', '']);
                break;
            case 'config':
                return implode(DIRECTORY_SEPARATOR, [$root, 'config', '']);
                break;
            case 'assets':
                return implode(DIRECTORY_SEPARATOR, [$root, 'app', 'assets', '']);
                break;
            default:
                return $root . DIRECTORY_SEPARATOR;

        }
    }

    public static function url()
    {
        return 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST');
    }

    public function get($key)
    {
        return Hash::get($this->yaml, $key);
    }

    public function set($key, $value)
    {
        return Hash::set($this->yaml, $key, $value);
    }

    public function keyExists($key)
    {
        return Hash::keyExists($this->yaml, $key);
    }

} 