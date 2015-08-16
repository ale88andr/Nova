<?php

class UsersController extends ApplicationController
{
    public function index()
    {
        $this->users = User::findById(1);
        $this->render('debug');
    }
}