<?php
namespace Models;

use Core\DB;

abstract class Users{
    # добавление нового пользвателя
    public static function addUser(string $login, string $password): bool
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $time = TIME;

        $q = DB::me()->prepare("INSERT IGNORE `users` (`login`, `password`,`time_create`) VALUES (:login,:password,:time_create)");
        $q->bindParam(':login', $login, \PDO::PARAM_STR);
        $q->bindParam(':password', $password, \PDO::PARAM_STR);
        $q->bindParam(':time_create', $time, \PDO::PARAM_INT);
        $q->execute();

        return DB::me()->lastInsertId() ? true : false;
    }
}
