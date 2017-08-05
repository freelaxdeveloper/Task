<?php
namespace More;

abstract class Misc{
    # проверяем правильность даты
    public static function validateDate($date, $format = "Y-m-d")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    public static function getTime(int $time, string $format = 'Y-m-d H:i'): string {
        $date = new \DateTime();
        $date->setTimestamp($time);
        return $date->format($format);
    }
}
