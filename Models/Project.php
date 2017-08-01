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
}
