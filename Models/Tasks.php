<?php
namespace Models;

use \Core\{DB,App};
use \Models\Task;

abstract class Tasks{
    /*
    * @param int time_start старт времени, по которому будут выводится задания
    * по умолчанию @param int time_start равно времени начала текущих суток
    в системе unix, то есть, выводятся актуальные задания на сегодня, что бы
    вывести задания например вчерашней давности этот параметр необходимо указать
    @param int time_start = mktime(0, 0, 0) - 3600 * 24
    * что бы вывести весь список заданий @param int time_start = 0
    */
    /*
    * @param int shit_days получение заданий за интервал времени
    * что бы получить задания за определенный интвервал, необходимо
    передать параметр @param int shit_days
    * @param int shit_days - смещение дней за которые будут выводится задания
    * Например:
    * @param int shit_days = 1 - только за сегодня
    * @param int shit_days = 7 - на неделю вперед
    * @param int shit_days = 30 - на месяц вперед (и.т.д.)
    * в примере предполагается что параметр @param int time_start используется по умолчанию
    * так же этот параметр (shit_days) не учитывается если передан параметр @param int time_start = 0
    */
    /*
    * @param int id_project
    * ID проекта задания которого необходимо вывести
    */
    /*
    * @param int status
    * статус выполнения проекта (1 не выполнено, 2 - выполнено)
    */
    public static function getTasks(?array $params = null)
    {
        $tasks = [];
        $where = '';
        $status = $params['status'] ?? 1;
        $id_project = $params['id_project'] ?? false;
        $shit_days = $params['shit_days'] ?? 1;
        $time_start = $params['time_start'] ?? mktime(0, 0, 0);

        # если время старта указано то берем задания за определенный интервал
        if ($time_start) {
            $time_end = $time_start + 3600 * 24 * $shit_days;
            $where .= ' AND `deadlines` < :time_end ';
        }
        # если указано ID проекта то берем задания этого проекта
        if ($id_project) {
            $where .= ' AND `id_project` = :id_project ';
        }

        $q = DB::me()->prepare("SELECT `id` FROM `tasks` WHERE `status` = :status AND `deadlines` > :time_start $where ORDER BY `deadlines` ASC, `importance` DESC");
        $q->bindParam(':status', $status, \PDO::PARAM_INT);
        $q->bindParam(':time_start', $time_start, \PDO::PARAM_INT);

        if ($time_start)
            $q->bindParam(':time_end', $time_end, \PDO::PARAM_INT);
        if ($id_project)
            $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);

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
