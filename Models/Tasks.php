<?php
namespace Models;

use \Core\{DB,App};
use \Models\Task;

abstract class Tasks{
    # получаем список всех заданий
    public static function getAll(int $status = 1): array
    {
        $tasks = [];
        $q = DB::me()->prepare("SELECT `tasks`.`id` FROM `tasks` WHERE `tasks`.`status` = :status ORDER BY `tasks`.`deadlines` ASC, `tasks`.`importance` DESC, `tasks`.`time_create` DESC");
        $q->bindParam(':status', $status, \PDO::PARAM_INT);
        $q->execute();
        $result = $q->fetchAll();
        foreach ($result as $task) {
            $tasks[] = new Task($task['id']);
        }
        return $tasks;
    }
    public static function getByProject(int $id_project, int $status = 1): array
    {
        $tasks = [];
        $q = DB::me()->prepare("SELECT `tasks`.`id` FROM `tasks` WHERE `tasks`.`id_project` = :id_project AND `tasks`.`status` = :status ORDER BY `tasks`.`deadlines` ASC, `tasks`.`importance` DESC, `tasks`.`time_create` DESC");
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->bindParam(':status', $status, \PDO::PARAM_INT);
        $q->execute();
        $result = $q->fetchAll();
        foreach ($result as $task) {
            $tasks[] = new Task($task['id']);
        }
        return $tasks;
    }
    # добавляем новое задание
    public static function create(string $message, int $deadlines, int $importance, int $id_project)
    {
        $id_user = App::user()->id;
        $time_create = TIME;

        $q = DB::me()->prepare("INSERT INTO `tasks` (`message`, `time_create`,`id_project`,`deadlines`,`importance`,`id_user`) VALUES (:message, :time_create, :id_project, :deadlines, :importance, :id_user)");
        $q->bindParam(':message', $message, \PDO::PARAM_STR);
        $q->bindParam(':time_create', $time_create, \PDO::PARAM_INT);
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->bindParam(':deadlines', $deadlines, \PDO::PARAM_INT);
        $q->bindParam(':importance', $importance, \PDO::PARAM_INT);
        $q->bindParam(':id_user', $id_user, \PDO::PARAM_INT);
        $q->execute();
    }
}
