<?php
namespace Core;

use \Models\User;
use \Core\Authorize;

abstract class App{
    /*
     * в любой не понятной ситуации ошибка 404
     */
    public static function access_denied($message = '')
    {
        if (true) {
            die($message);
        }
        header("HTTP/1.1 404 Not Found");
        exit;
    }
    public static function user()
    {
        static $_instance;
        if (!$_instance) {
            $_instance = new User(Authorize::getId());
            # если почему-то хэш пользователя не совпадает с тем что в сессии
            # сбрасываем авторизацию
            if ($_instance->password != Authorize::getHash()) {
                Authorize::exit();
            }
        }
        return $_instance;
    }
}
