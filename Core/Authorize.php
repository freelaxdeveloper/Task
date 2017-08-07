<?php
namespace Core;

abstract class Authorize{
    // ключ, с которым будет зранится пользовательский ID
    const KEY_USER_ID = 'id';
    // ключ, с которым будет зранится пользовательский хэш пароля 
    const KEY_USER_PASSWORD = 'what_is_it';

    # авторизируемся
    public static function authorized(int $id_user, string $hash_pass)
    {
        if (self::isAuthorize()) {
            return;
        }
        $_SESSION[self::KEY_USER_ID] = $id_user;
        $_SESSION[self::KEY_USER_PASSWORD] = $hash_pass;
    }
    # проверям авторизованы ли
    public static function isAuthorize(): bool
    {
        return self::getId() ? true : false;
    }
    # получаем ID пользователя
    public static function getId(): int
    {
        return $_SESSION[self::KEY_USER_ID] ?? 0;
    }
    # получаем хэш его пароля
    public static function getHash(): string
    {
        return $_SESSION[self::KEY_USER_PASSWORD] ?? 0;
    }
    # выходим с авторизации
    public static function exit()
    {
        unset($_SESSION[self::KEY_USER_ID]);
        unset($_SESSION[self::KEY_USER_PASSWORD]);
    }
}
