<?php

namespace Nova\Helpers;

class Hash
{
    public static function add($array, $key, $value)
    {
        if(is_null(static::get($array, $key)))
        {
            static::set($array, $key, $value);
        }
    }

    public static function get($array, $key)
    {
        if(is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment)
        {
            if (!is_array($array) || !array_key_exists($segment, $array)) {return null;}
            $array = $array[$segment];
        }

        return $array;
    }

    public static function set(&$array, $key, $value)
    {
        if(is_null($key)) {
            return $value;
        }

        $array[$key] = $value;

        return $array;
    }

    public static function extract(&$array)
    {
        if(empty($array) || static::first($array) == '') {
            return null;
        }
        else {
            return array_shift($array);
        }
    }

    public static function first($array)
    {
        return $array[0];
    }

    public static function last($array)
    {
        return array_pop($array);
    }

    public static function keyExists($array, $key)
    {
        if(!is_array($array) || empty($array)){
            return false;
        }

        return array_key_exists($key, $array);
    }

    public static function contains($array, $data)
    {
        if(!empty($array) && !empty($data)) {
            return in_array($data, $array) ? true : false;
        } else {
            return false;
        }
    }
}