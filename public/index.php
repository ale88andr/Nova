<?php

require_once __DIR__ . '/../src/Nova/Core/Application.php';

Application::initialize();

//$app = new UsersController();
//$app->index();

new HomeController();