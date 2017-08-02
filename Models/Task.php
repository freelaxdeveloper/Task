<?php
namespace Models;

use \Core\DB;

abstract class Task{
    # получаем список всех заданий
    static function getAll(): array
    {
        $q = DB::me()->query("SELECT `tasks`.*, `projects`.`title`, `projects`.`color` FROM `tasks` INNER JOIN `projects` ON `tasks`.`id_project` = `projects`.`id` ORDER BY `tasks`.`id` DESC");
        if ($tasks = $q->fetchAll()) {
            return $tasks;
        }
        return [];
    }
    public static function getByProject(int $id_project): array
    {
        $q = DB::me()->prepare("SELECT `tasks`.*, `projects`.`title`, `projects`.`color` FROM `tasks` INNER JOIN `projects` ON `tasks`.`id_project` = `projects`.`id` AND `projects`.`id` = :id_project ORDER BY `tasks`.`id` DESC");
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->execute();
        if ($tasks = $q->fetchAll()) {
            return $tasks;
        }
        return [];
    }
}
