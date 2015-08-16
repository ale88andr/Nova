<?php

class UsersController extends ApplicationController
{
    public function index()
    {
        $this->users = User::all();
        $this->render();
    }

    public function create()
    {
        $user = new User();
        $user->login = 'ale88andr';
        $user->email = 'ale88andr@mail.ru';

        $user->create();
    }

    public function update()
    {
        $user = User::findById(5);
        $user->email = 'ale88andr@gmail.com';

        $user->update();
    }
}