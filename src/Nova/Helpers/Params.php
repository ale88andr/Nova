<?php

namespace Nova\Helpers;

class Params
{
    public static function permit($params = [], $accessList = [])
    {
        foreach($params as $key => $value)
        {
            if(!in_array($key, $accessList)){
                unset($params[$key]);
            }
        }

        return $params;
    }
}