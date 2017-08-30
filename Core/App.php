<?php
namespace Core;

use \Models\User;
use \Core\Authorize;

abstract class App{
    const USER_GROUP_USER = 1;
    const USER_GROUP_MODER = 2;
    const USER_GROUP_ADMIN = 3;

    /*
     * в любой не понятной ситуации ошибка 404
     */
    public static function access_denied(string $message = '', bool $view = false)
    {
        // администратору/модератору можно показать ошибки
        if (self::user()->group >= self::USER_GROUP_MODER || $view) {
            die($message);
        }
        header("HTTP/1.1 404 Not Found");
        exit;
    }
    # авторизация пользователя
    public static function user()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new User(Authorize::getId());
            if ($_instance->id && $_instance->token_time_update < TIME) {
                $_instance->updateToken();
            }
            # если почему-то хэш пользователя не совпадает с тем что в сессии
            # сбрасываем авторизацию
            if ($_instance->password != Authorize::getHash()) {
                Authorize::exit();
                self::access_denied('Ошибка авторизации', true);
            }
        }
        return $_instance;
    }
    # возвращаем референую ссылку, если таковой нету то заданую
    public static function referer(string $link = '/'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $link;
    }
}
