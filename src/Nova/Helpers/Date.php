<?php

namespace Nova\Helpers;

use \DateTime;

class Date
{
    public static function now($leadZero = false, $separator = '/')
    {
        $date = self::constructDate();
        if($leadZero){
            return $date->format('m' . $separator . 'd' . $separator . 'Y');
        }

        return $date->format('n' . $separator . 'j' . $separator . 'Y');
    }

    public static function current($leadZero = false, $separator = '/')
    {
        $date = self::constructDate();
        if($leadZero){
            return $date->format('d' . $separator . 'm' . $separator . 'Y');
        }

        return $date->format('j' . $separator . 'n' . $separator . 'Y');
    }

    public static function year($date = false)
    {
        if(!$date){
            return self::constructDate()->format('Y');
        }

        return self::constructDate($date)->format('Y');
    }

    public static function month($date = false)
    {
        if(!$date){
            return self::constructDate()->format('n');
        }

        return self::constructDate($date)->format('n');
    }

    public static function day($date = false)
    {
        if(!$date){
            return self::constructDate()->format('j');
        }

        return self::constructDate($date)->format('j');
    }

    public static function month_name($date = false)
    {
        return self::constructDate()->format('F');
    }

    public static function diff($dateStart, $dateEnd)
    {
        $start = gmmktime(0, 0, 0, self::month($dateStart), self::day($dateStart), self::year($dateStart));
        $end = gmmktime(0, 0, 0, self::month($dateEnd), self::day($dateEnd), self::year($dateEnd));

        return ($end - $start) / (60 * 60 * 24);
    }

    private static function constructDate($date = 'now')
    {
        return new DateTime($date);
    }
}