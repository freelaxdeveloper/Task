<?php
namespace App\Core;

use \App\Models\User;
use \App\Core\{Authorize,Language};

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
            $_instance = User::find(Authorize::getId());
            if ($_instance->id && $_instance->token_time_update < TIME) {
                //$_instance->updateToken();
            }
            # если почему-то хэш пользователя не совпадает с тем что в сессии
            # сбрасываем авторизацию
            if ($_instance->id && $_instance->password != Authorize::getHash()) {
                Authorize::exit();
                self::access_denied(__('Ошибка авторизации'), true);
            }
        }
        return $_instance;
    }
    # возвращаем референую ссылку, если таковой нету то заданую
    public static function referer(string $link = '/'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $link;
    }
    # выбранный язык сайта
    public static function language()
    {
        static $language;

        if (!$language) {
            $language = new Language(self::current_language());
        }
        return $language;
    }
    # получаем язык сайта из адресной строки
    private static function current_language(): string
    {
        $language_current = explode('/', self::getURI())[0];
        return $language_current;
    }

    public static function getURI(): string
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }
    # возвращаем URL
    public static function url(string $url): string
    {
        return '/' . self::current_language() . $url;
    }
    # получаем данные настроек
    public static function config(string $file, bool $process_sections = false): array
    {
        $path_file = H . '/System/config/' . $file . '.ini';
        if (!file_exists($path_file)) {
            return ['error'];
        }
        $config = parse_ini_file($path_file, $process_sections);
        return $config;
    }
 }
