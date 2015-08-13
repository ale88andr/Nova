<?php

class UsersController {
    public function index()
    {
        $users = User::all();
        include __DIR__ . '/../views/users/index.html.php';
    }
} 