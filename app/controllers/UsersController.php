<?php

use Nova\Helpers\Input;
use Nova\Helpers\Hash;

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
        $user->login = Hash::get($_POST, 'users.login');
        $user->email = Hash::get($_POST, 'users.email');

        print_r($user->save());

        if($user->save() > 0){
            echo 'user created';
        } else {
            $this->render('/users/register');
        }
    }

    public function update()
    {
        $user = User::findById(1);
        $user->permit(['email']);
        $user->email = 'ale88andr@inbox.ru';
        $user->login = 'blah';

        $user->update();
    }

    public function login()
    {
        $this->render();
    }

    public function register()
    {
        if(Input::isPost()){
            $this->create();
        } else {
            $this->render();
        }
    }
}