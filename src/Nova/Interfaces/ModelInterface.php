<?php

namespace Nova\Interfaces;

interface ModelInterface
{
    public static function all($fields);

    public static function find($fields, $select);

    public static function findById($id, $fields);

    public static function insert($hashValues);

    public static function update($fields, $hashValues);
}