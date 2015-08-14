<?php

class UsersController
{

    public function index()
    {
        $users = User::find('blah');
        include __DIR__ . '/../views/users/index.html.php';
    }
} 