<?php
namespace Models;

use \Core\DB;

/**
 * Работа с проектами
 */
abstract class Project
{
    # получаем список всех проектов
    static function getAll(): array
    {
        $q = DB::me()->query("SELECT * FROM `projects` ORDER BY `id` DESC");
        if ($projects = $q->fetchAll()) {
            return $projects;
        }
        return [];
    }
    # получем проект по его ID
    public static function getOne(int $id_project): array
    {
        $q = DB::me()->prepare("SELECT * FROM `projects` WHERE `id` = :id LIMIT 1");
        $q->bindParam(':id', $id_project, \PDO::PARAM_INT);
        $q->execute();
        if ($project = $q->fetch()) {
            return $project;
        }
        return [];
    }
}
