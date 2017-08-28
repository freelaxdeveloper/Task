<?php
namespace Models;

abstract class Captcha{
    private static function generate(): string
    {
        $chars = 'abdefhknrstyz23456789';
        $length = rand(4, 7); // длина капчи
        $numChars = strlen($chars); // Узнаем, сколько у нас задано символов
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, $numChars), 1);
        }
        // Перемешиваем, на всякий случай
        $array_mix = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
        srand((float)microtime() * 1000000);
        shuffle($array_mix);
        $captcha = implode('', $array_mix);
        $_SESSION['captcha'] = $captcha;
        return $captcha;
    }
    private static function getFont(): string
    {
        $fonts = glob(H . '/Static/fonts/*.ttf');
        return $fonts[mt_rand(0, count($fonts) - 1)];
    }
    public static function image()
    {
        $captcha_font = self::getFont();
        $captcha_text = self::generate();
        //$captcha_text = basename($captcha_font);

        $image = imagecreatetruecolor(110, 40);
        $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $color);
        imagesavealpha($image, true);
        $colour = imagecolorallocate($image, 0, 0, 250);
        $rotate = rand(-2, 3);
        imagettftext($image, 19, $rotate, 6, 30 , $colour, $captcha_font, $captcha_text);
        header('Content-Type: image/png');
        ImagePNG($image);
    }
    public static function check(): bool
    {
        $send_captcha = mb_strtolower($_POST['captcha']) ?? '';
        return $send_captcha == $_SESSION['captcha'] ? true : false;
    }
}
