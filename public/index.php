<?php

require_once __DIR__ . '/../src/Nova/Core/Application.php';
require_once __DIR__ . '/../vendor/autoload.php';

Application::initialize();

$app = new UsersController();
$app->index();

//$env = new Nova\Core\Env();
//var_dump($env->get('defaults.environment'));