<?php
namespace App\Models;

use \App\Core\DB;

abstract class Distributions{
    public static function getDistribution(): array
    {
        $current_data = date('Y-m-d H');
        $current_hours = date('H');
/*
        $q = DB::me()->prepare(
            "SELECT `distribution`.`count_install` AS 'install_all',`distribution`.`title`,`distribution`.`xpath`,`distribution`.`link`,
            `distribution_install`.`count` AS 'install_hours',
            `distribution_settings`.`count` AS 'install_limit'
            FROM `distribution` 
            LEFT JOIN `distribution_install` ON `distribution`.`id` = `distribution_install`.`id_distribution` AND `date` = :date 
            INNER JOIN `distribution_settings` ON `distribution_settings`.`id_distribution` = `distribution`.`id` AND `distribution_settings`.`hours` = :hours 
            WHERE (`distribution_install`.`count` < `distribution_settings`.`count` OR `distribution_install`.`count` IS NULL) 
            ORDER BY `distribution_install`.`count` ASC
        ");
*/
        $q = DB::me()->prepare(
            "SELECT `distribution`.`id`,`distribution`.`xpath`,`distribution`.`link`,
            `distribution_install`.`count` AS 'install_hours'
            FROM `distribution` 
            LEFT JOIN `distribution_install` ON `distribution`.`id` = `distribution_install`.`id_distribution` AND `date` = :date 
            INNER JOIN `distribution_settings` ON `distribution_settings`.`id_distribution` = `distribution`.`id` AND `distribution_settings`.`hours` = :hours 
            WHERE (`distribution_install`.`count` < `distribution_settings`.`count` OR `distribution_install`.`count` IS NULL) 
            ORDER BY `distribution_install`.`count` ASC LIMIT 1
        ");
        $q->bindParam(':date', $current_data, \PDO::PARAM_STR);
        $q->bindParam(':hours', $current_hours, \PDO::PARAM_INT);
        $q->execute();
        if ($distribution = $q->fetch()) {
            $distribution['install_hours'] = $distribution['install_hours'] ?? 0;
            return $distribution;
        }
        return [];
    }
    public static function getDistributions(array $params = []): array
    {
        $where = '';
        $id = $params['id'] ?? false;

        if ($id) {
            $where .= " AND `d`.`id` = :id ";
        }
        $current_data = date('Y-m-d H');
        $current_hours = date('H');
        $q = DB::me()->prepare(
            "SELECT `s`.`count_install` AS 'install_all',`d`.`title`,`d`.`id`,`d`.`xpath`,`d`.`link`,
            `i`.`count` AS 'install_hours',
            `distribution_settings`.`count` AS 'install_limit'
            FROM (SELECT SUM(`count`) AS `count_install`, `id_distribution`
                FROM `distribution_settings` GROUP BY `id_distribution`) AS `s`, `distribution` AS `d`
            LEFT JOIN `distribution_install` AS `i` ON `d`.`id` = `i`.`id_distribution` AND `date` = :date 
            LEFT JOIN `distribution_settings` ON `distribution_settings`.`id_distribution` = `d`.`id` AND `distribution_settings`.`hours` = :hours 
            WHERE `s`.`id_distribution` = `d`.`id` $where
            ORDER BY `i`.`count` ASC
        ");
        $q->bindParam(':date', $current_data, \PDO::PARAM_STR);
        $q->bindParam(':hours', $current_hours, \PDO::PARAM_INT);
        if ($id) {
            $q->bindParam(':id', $id, \PDO::PARAM_INT);
            $q->execute();
            if ($fetch = $q->fetch()) {
                return $fetch;
            } else {
                return [];
            }
        }
        $q->execute();
        if ($distributions = $q->fetchAll()) {
            return $distributions;
        }
        return [];
    }
    public static function updateInstall(int $id, int $count_install)
    {
        $date = date('Y-m-d H');
        $new_count_install = $count_install + 1;
        if ($count_install) {
            $q = DB::me()->prepare("UPDATE `distribution_install` SET `count` = :count WHERE `id_distribution` = :id AND `date` = :date LIMIT 1");
            $q->bindParam(':count', $new_count_install, \PDO::PARAM_INT);
            $q->bindParam(':id', $id, \PDO::PARAM_INT);
            $q->bindParam(':date', $date, \PDO::PARAM_STR);
            $q->execute();
        } else {
            $q = DB::me()->prepare("INSERT INTO `distribution_install` (`id_distribution`,`date`) VALUES (:id,:date)");
            $q->bindParam(':id', $id, \PDO::PARAM_INT);
            $q->bindParam(':date', $date, \PDO::PARAM_STR);
            $q->execute();
        }
    }
    public static function updateSettings(int $id_distribution, int $hours, int $count)
    {
        $q = DB::me()->query("SELECT `id` FROM `distribution_settings` WHERE `id_distribution` = '$id_distribution' AND `hours` = '$hours' LIMIT 1");
        if ($q->fetch()) {
            $q = DB::me()->prepare("UPDATE `distribution_settings` SET `count` = ? WHERE `id_distribution` = ? AND `hours` = ? LIMIT 1");
        } else {
            $q = DB::me()->prepare("INSERT INTO `distribution_settings` (`count`,`id_distribution`,`hours`) VALUES (?,?,?)");
        }
        $q->execute([$count, $id_distribution, $hours]);
        return;
    }
    public static function updatePercents(int $id_distribution, int $hours, int $percent)
    {
        $q = DB::me()->query("SELECT `id` FROM `distribution_percent` WHERE `id_distribution` = '$id_distribution' AND `hours` = '$hours' LIMIT 1");
        if ($q->fetch()) {
            $q = DB::me()->prepare("UPDATE `distribution_percent` SET `percent` = ? WHERE `id_distribution` = ? AND `hours` = ? LIMIT 1");
        } else {
            $q = DB::me()->prepare("INSERT INTO `distribution_percent` (`percent`,`id_distribution`,`hours`) VALUES (?,?,?)");
        }
        $q->execute([$percent, $id_distribution, $hours]);
        return;
    }
    public static function getSettings(int $id_distribution): array
    {
        $hours = DB::me()->query("SELECT * FROM `distribution_settings` WHERE `id_distribution` = '$id_distribution' ORDER BY `hours` ASC")->fetchAll();
        return $hours;
    }
}