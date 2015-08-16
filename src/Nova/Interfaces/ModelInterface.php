<?php

namespace Nova\Interfaces;

interface ModelInterface
{
    public static function all($fields);

    public static function find($fields, $select);

    public static function findById($id, $fields);

    public function create();

    public function update();
}