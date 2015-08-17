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
        $user = User::findById(1);
        $user->permit(['email']);
        $user->email = 'ale88andr@inbox.ru';
        $user->login = 'blah';

        $user->update();
    }
}