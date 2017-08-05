<?php
namespace Models;

use \Core\{DB,App};

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
        $current_time = TIME;
        $q = DB::me()->prepare("SELECT `t`.`id_project`, `p`.`title`, `p`.`id`, `p`.`color`, `t`.`task_count`,
       `t`.`task_lose`, `t`.`task_active`
  FROM (
    SELECT `id_project`, COUNT(*) AS `task_count`,
           SUM(`status` = '1' AND `deadlines` < :TIME) AS `task_lose`,
           SUM(`status` = '1') AS `task_active`
      FROM `tasks`
      GROUP BY `id_project`
  ) AS `t`
  RIGHT JOIN `projects` AS `p` ON `p`.`id` = `t`.`id_project`
  ORDER BY `t`.`task_lose` DESC, `t`.`task_active` DESC,
           `t`.`task_count` DESC, `p`.`id` DESC");
           $q->bindParam(':TIME', $current_time, \PDO::PARAM_INT);
           $q->execute();
        if ($projects = $q->fetchAll()) {
            return $projects;
        }
    }
    # получем проект по его ID
    public static function getOne(int $id_project): array
    {
        $q = DB::me()->prepare("SELECT * FROM `projects` WHERE `id` = :id LIMIT 1");
        $q->bindParam(':id', $id_project, \PDO::PARAM_INT);
        $q->execute();
        $project = $q->fetch();
    }
    # удаляем один проект
    public static function deleteOne(int $id_project): bool
    {
        // удаляем все завершенные заказы с проекта
        $q = DB::me()->prepare("DELETE FROM `tasks` WHERE `id_project` = :id_project AND `status` = '2'");
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->execute();
        // удаляем проект
        try {
            $q = DB::me()->prepare("DELETE FROM `projects` WHERE `id` = :id LIMIT 1");
            $q->bindParam(':id', $id_project, \PDO::PARAM_INT);
            $q->execute();
        } catch (\Exception $e) {
            return false;
        }
        return true;
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
    public static function update(string $title, string $color, int $id_project)
    {
        $q = DB::me()->prepare("UPDATE `projects` SET `title` = :title, `color` = :color WHERE `id` = :id_project LIMIT 1");
        $q->bindParam(':title', $title, \PDO::PARAM_STR);
        $q->bindParam(':color', $color, \PDO::PARAM_STR);
        $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        $q->execute();
    }
}
