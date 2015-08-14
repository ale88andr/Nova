<?php

namespace Nova\Core;

use Symfony\Component\Yaml\Yaml;
use Nova\Helpers\Hash;
use Nova\Interfaces\AccessInterface;

class Env implements AccessInterface {

    private $yaml;

    public function __construct($ymlFile = null)
    {
        $ymlFile = is_null($ymlFile)
            ? filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.yml'
            : $ymlFile;
        $this->yaml = Yaml::parse(file_get_contents($ymlFile));
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