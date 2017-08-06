<?php
namespace Core;

abstract class Authorize{
    # авторизируемся
    public static function authorized(int $id_user, string $hash_pass)
    {
        if (self::isAuthorize()) {
            return;
        }
        $_SESSION['id'] = $id_user;
        $_SESSION['what_is_it'] = $hash_pass;
    }
    # проверям авторизованы ли
    public static function isAuthorize(): bool
    {
        return self::getId() ? true : false;
    }
    # получаем ID пользователя
    public static function getId(): int
    {
        return $_SESSION['id'] ?? 0;
    }
    # получаем хэш его пароля
    public static function getHash(): string
    {
        return $_SESSION['what_is_it'] ?? 0;
    }
    # выходим с авторизации
    public static function exit()
    {
        unset($_SESSION['id']);
        unset($_SESSION['what_is_it']);
    }
}
