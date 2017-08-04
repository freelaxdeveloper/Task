<?php
namespace More;

abstract class Misc{
    # проверяем правильность даты
    public static function validateDate($date, $format = "Y-m-d")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
