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
}
