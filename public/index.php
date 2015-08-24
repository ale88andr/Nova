<?php

require_once __DIR__ . '/../src/Nova/Core/Application.php';
require_once __DIR__ . '/../vendor/autoload.php';

Application::initialize();

$app = new Application();
require_once $app->layout();

//$app = new UsersController();
//$app->update();

//$env = new Nova\Core\Env();
//var_dump($env->get('defaults.environment'));