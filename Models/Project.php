<?php
namespace Models;

use \Core\{DB, App};

class Project{
    private $id;
    private $data;

    public function __construct(int $id_project)
    {
        $this->id = $id_project;
        $this->data = $this->getData();
    }
    # управление группой
    public function management(): bool
    {
        // автору всегда можно управлять
        if ($this->data['id_user'] == App::user()->id) {
            return true;
        }
        // если включено управление всем, разрешаем авторизованным
        if ($this->data['set_management'] == 1 && App::user()->id) {
            return true;
        }
        return false;
    }
    # получение данных проекта
    private function getData(): array
    {
        $current_time = TIME;
        $q = DB::me()->prepare("SELECT `t`.`id_project`, `p`.`title`, `p`.`id`, `p`.`color`, `p`.`id_user`, `p`.`set_management`, `t`.`task_count`,
       `t`.`task_lose`, `t`.`task_active`
        FROM (
            SELECT `id_project`, COUNT(*) AS `task_count`,
                SUM(`status` = '1' AND `deadlines` < :TIME) AS `task_lose`,
                SUM(`status` = '1') AS `task_active`
            FROM `tasks`
            GROUP BY `id_project`
        ) AS `t`
        RIGHT JOIN `projects` AS `p` ON `p`.`id` = `t`.`id_project`
        WHERE `p`.`id` = :id_project LIMIT 1");
        $q->bindParam(':id_project', $this->id, \PDO::PARAM_INT);
        $q->bindParam(':TIME', $current_time, \PDO::PARAM_INT);
        $q->execute();
        if ($project = $q->fetch()) {
            return $project;
        }
        return ['title' => 'None', 'id' => 0];
    }
    # удаляем проект
    public function delete(): bool
    {
        // удалять можно только автору
        if ($this->data['id_user'] != App::user()->id) {
            return false;
        }
        // если есть активные задачи, то удалять проект нельзя
        if ($this->data['task_active']) {
            return false;
        }
        // удаляем все завершенные заказы с проекта
        $q = DB::me()->prepare("DELETE FROM `tasks` WHERE `id_project` = :id_project AND `status` = '2'");
        $q->bindParam(':id_project', $this->id, \PDO::PARAM_INT);
        $q->execute();
        // удаляем проект
        try {
            $q = DB::me()->prepare("DELETE FROM `projects` WHERE `id` = :id LIMIT 1");
            $q->bindParam(':id', $this->id, \PDO::PARAM_INT);
            $q->execute();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public function __get($name): string
    {
        return $this->data[$name] ?? '';
    }
    public function __isset($name): bool
    {
        return isset($this->data[$name]) ? true : false;
    }
}
