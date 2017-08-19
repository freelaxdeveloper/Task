<?php
namespace Models;

use \Core\DB;
use \Models\User;

abstract class Users{
    # добавление нового пользвателя
    public static function addUser(string $login, string $password): array
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $time = TIME;

        $q = DB::me()->prepare("INSERT IGNORE `users` (`login`, `password`,`time_create`) VALUES (:login,:password,:time_create)");
        $q->bindParam(':login', $login, \PDO::PARAM_STR);
        $q->bindParam(':password', $password, \PDO::PARAM_STR);
        $q->bindParam(':time_create', $time, \PDO::PARAM_INT);
        $q->execute();

        if ($id = DB::me()->lastInsertId()) {
            return ['id' => $id, 'password' => $password];
        }
        return ['error' => 'Введенный вами логин уже существует'];
    }
    # получаем данные по логину и паролю
    /*
     так как PHP7.1 возможности пока что использовать нету, использовать :?int не получится
     потому пока что возвращаемое значение указывать не буду
    */
    public static function getUserByPassword(string $login, string $password)
    {
        $q = DB::me()->prepare("SELECT `id`,`password` FROM `users` WHERE `login` = :login LIMIT 1");
        $q->bindParam(':login', $login, \PDO::PARAM_STR);
        $q->execute();
        if (!$user = $q->fetch()) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        return $user;
    }
    public static function getAll(): array
    {
        $users = [];
        $q = DB::me()->query("SELECT `id` FROM `users` ORDER BY `id` DESC");
        $res = $q->fetchAll();
        foreach ($res AS $user) {
            $users[] = new User($user['id']);
        }
        return $users;
    }
}
