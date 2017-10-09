<?php
namespace App\Models;

use \App\Core\{DB,App};
use \App\Models\Task;

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
    public static function getTasks(array $params = []): array
    {
        $tasks = [];
        $where = '';
        $status = $params['status'] ?? false;
        $id_project = $params['id_project'] ?? false;
        $shit_days = $params['shit_days'] ?? 1;
        $time_start = $params['time_start'] ?? 0;
        $my_task = $params['my_task'] ?? false;
        $id_task = $params['id'] ?? false;

        # если время старта указано то берем задания за определенный интервал
        if ($time_start) {
            $time_end = $time_start + 3600 * 24 * $shit_days;
            $where .= ' AND `tasks`.`deadlines` < :time_end ';
        }
        # если указано ID проекта то берем задания этого проекта
        if ($id_project) {
            $where .= ' AND `tasks`.`id_project` = :id_project ';
        }
        # показываем только свои задачи
        if ($my_task) {
            $where .= ' AND (`tasks`.`id_user` = :id_user OR `projects`.`id_user` = :id_project_user) ';
        }
        # показываем выбранную задачу
        if ($id_task) {
            $where .= ' AND `tasks`.`id` = :id ';
        }
        # показываем выбранный статус
        if ($status) {
            $where .= ' AND `tasks`.`status` = :status ';
        }

        $q = DB::me()->prepare("SELECT `tasks`.*, `projects`.`id_user` AS 'id_user_project', `projects`.`title`, `projects`.`color`, `users`.`login`
            FROM `tasks`, `projects`, `users`
            WHERE `users`.`id` = `tasks`.`id_user` AND `projects`.`id` = `tasks`.`id_project` AND `tasks`.`deadlines` > :time_start $where
            ORDER BY `tasks`.`deadlines` ASC, `tasks`.`importance` DESC, `tasks`.`id` DESC" . ($id_task ? ' LIMIT 1' : null));
        $q->bindParam(':time_start', $time_start, \PDO::PARAM_INT);

        if ($status)
            $q->bindParam(':status', $status, \PDO::PARAM_INT);
        if ($time_start)
            $q->bindParam(':time_end', $time_end, \PDO::PARAM_INT);
        if ($id_project)
            $q->bindParam(':id_project', $id_project, \PDO::PARAM_INT);
        if ($my_task) {
            $id_user = App::user()->id;
            $q->bindParam(':id_user', $id_user, \PDO::PARAM_INT);
            $q->bindParam(':id_project_user', $id_user, \PDO::PARAM_INT);
        }
        if ($id_task) {
            $q->bindParam(':id', $id_task, \PDO::PARAM_INT);
        }
        $q->execute();

        if ($id_task && $task = $q->fetch()) {
            return $task;
        }
        $result = $q->fetchAll();
        foreach ($result as $task) {
            $tasks[] = new Task($task);
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
