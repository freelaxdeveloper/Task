<?php
namespace Libraries\More;

/*
* ф-ции для работы с текстом
*/
abstract class Text{
    # убираем html теги
    public static function input_text(string $text): string
    {
        $text = filter_var($text, FILTER_SANITIZE_STRING);
        return trim($text);
    }
    # допускаем строки подходящие для названий
    # без.спец.символов, разрешен лишь пробел и нижнее подчеркивание, диапазон от 3 до 32 символа
    public static function for_name(string $text): string
    {
        $text = self::input_text($text);
        if (preg_match('/^[а-яa-z0-9|\s|\_]{3,32}$/ui', $text)) {
            return $text;
        }
        return '';
    }
}
