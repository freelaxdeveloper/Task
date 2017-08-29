<?php
namespace Models;

class Captcha{
    public $font; // шрифт
    public $text; // отображаемый текст
    public $string = 'qwertyuipasdfghjklzxcvbnm123456789'; // текст, с которого будет формироватся капча
    public $length; // количество символов в капче

    public function __construct()
    {
        $this->font = $this->getFont();
        $this->length = $this->getLength();
    }
    # генерируем строку для капчи
    private function generate()
    {
        for ($i = 0; $i < $this->length; $i++) {
            $this->text .= $this->getSymbol();
        }
    }
    # получем длину капчи
    private function getLength(): int
    {
        return mt_rand(4, 7);
    }
    # получаем случайный символ
    private function getSymbol()
    {
        return substr($this->string, mt_rand(0, strlen($this->string)), 1);
    }
    # получаем случайный шрифт
    private function getFont(): string
    {
        $fonts = glob(H . '/Static/fonts/*.ttf');
        return $fonts[mt_rand(0, count($fonts) - 1)];
    }
    # показываем капчу
    public function show()
    {
        $this->generate();
        $image = imagecreatetruecolor(110, 40);
        $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $color);
        imagesavealpha($image, true);
        $colour = imagecolorallocate($image, 0, 0, 250);
        $rotate = rand(-2, 3);
        $font_size = mt_rand(15, 19);
        imagettftext($image, $font_size, $rotate, 6, 30 , $colour, $this->font, $this->text);
        header('Content-Type: image/png');
        ImagePNG($image);
    }
    # проверяем правильность ввода капчи
    public static function check(): bool
    {
        $send_captcha = mb_strtolower($_POST['captcha']) ?? '';
        $captcha = $_SESSION['captcha'];
        unset($_SESSION['captcha']);
        return $send_captcha == $captcha ? true : false;
    }
    public function __destruct()
    {
        $_SESSION['captcha'] = $this->text;
    }
}
