<?php
namespace More;

/*
* разные полезные ф-ции
*/
abstract class Misc{
    # проверяем правильность даты
    public static function validateDate($date, $format = "Y-m-d")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    # вывод времени unix в читаемом виде
    public static function getTime(int $time, string $format = 'Y-m-d H:i'): string {
        $date = new \DateTime();
        $date->setTimestamp($time);
        return $date->format($format);
    }
    /**
   * Склонение строки согласно переданному числу
   * titles example: ['рубль', 'рубля', 'рублей']
   *
   * @param $number
   * @param $titles
   * @param bool $returnNumber
   * @return string
   */
  public function declension($number, $titles, $returnNumber = false)
  {
    $cases = [2, 0, 1, 1, 1, 2];

    return ($returnNumber ? $number . ' ' : '') . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
  }
}
