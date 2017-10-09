<?php
namespace App\Models;

use \App\Core\{DB,App};

/**
 * Работа с проектами
 */
abstract class Projects
{
    /*
    * получаем список всех проектов
    - task_lose количество просроченных заданий
    - task_active количество не выполненных заданий
    */
    static function getAll(): array
    {
        static $projects;
        if ($projects) {
            return $projects;
        }
        $projects = [];
        $count['tasc_active'] = $count['task_lose'] = $count['task_count'] = [];
        $q = DB::me()->query("SELECT `id` FROM `projects`");
        $res = $q->fetchAll();
        $i = 0;
        foreach ($res as $project) {
            $projects[$i] = new Project($project['id']);
            $count['task_lose'][$i] = $projects[$i]->task_lose;
            $count['tasc_active'][$i] = $projects[$i]->task_active;
            $count['task_count'][$i] = $projects[$i]->task_count;
            $i++;
        }
        array_multisort($count['task_lose'], SORT_DESC, $count['tasc_active'], SORT_DESC, $count['task_count'], SORT_DESC, $projects);        return $projects;
    }
    # создаем проект
    public static function create(string $title, string $color)
    {
        $id_user = App::user()->id;
        $time_create = TIME;

        $q = DB::me()->prepare("INSERT INTO `projects` (`title`, `color`,`id_user`,`time_create`) VALUES (:title, :color, :id_user, :time_create)");
        $q->bindParam(':title', $title, \PDO::PARAM_STR);
        $q->bindParam(':color', $color, \PDO::PARAM_STR);
        $q->bindParam(':id_user', $id_user, \PDO::PARAM_INT);
        $q->bindParam(':time_create', $time_create, \PDO::PARAM_INT);
        $q->execute();
    }
    # создаем проект
    public static function update(string $title, string $color, int $set_management, int $id_project)
    {
        $q = DB::me()->prepare("UPDATE `projects` SET `title` = :title, `color` = :color, `set_management` = :set_management WHERE `id` = :id_project LIMIT 1");
        $q->bindParam(':title', $title, \PDO::PARAM_STR);
        $q->bindParam(':color', $color, \PDO::PARAM_STR);
        $q->bindParam(':set_management', $set_management, \PDO::PARAM_INT);
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->execute();
    }
}
