<?php
namespace Models;

use \Core\DB;
use \More\Misc;

class Task{
    private $id;
    private $data;
    private $_update = false; # метка, стоит ли обновлять данные в БД

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->data = $this->getData();
    }
    # получаем данные задания
    private function getData(): array
    {
        $q = DB::me()->prepare("SELECT `tasks`.*, `projects`.`title`, `projects`.`color`, `users`.`login`
            FROM `tasks`
            INNER JOIN `users` ON `tasks`.`id_user` = `users`.`id`
            INNER JOIN `projects` ON `tasks`.`id_project` = `projects`.`id`
            WHERE `tasks`.`id` = :id LIMIT 1");
        $q->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $q->execute();
        if ($task = $q->fetch()) {
            return $task;
        }
        return ['id' => 0];
    }
    /*
    * записыватся дата будет в формате Y-m-dTH:i
    * но в базу данных будем сохранять в формате UNIX
    */
    private function setDeadlines(string $data): int
    {
        $date = new \DateTime($data);
        return $date->format('U');
    }
    # в зависимотси от важности определяем цвет
    public function getImportance()
    {
        switch ($this->data['importance']) {
            case 0:
                return 'green'; // не очень важно
            case 1:
                return 'orange'; // важно
            case 2:
                return 'red'; // очень важно
        }
    }
    public function getStrImportance()
    {
        switch ($this->data['importance']) {
            case 0:
                return 'Не очень важно'; // не очень важно
            case 1:
                return 'Важно'; // важно
            case 2:
                return 'Очень важно'; // очень важно
        }
    }
    # взависимости от цвета определяем его важность
    public static function setImportance(string $importance): int
    {
        switch ($importance) {
            case 'green':
                return 0; // не очень важно
            case 'orange':
                return 1; // важно
            case 'red':
                return 2; // очень важно
            default:
                return 0; // по умолчанию не очень важно
        }
    }
    # проверяем истекло ли время выполнения задачи
    public function lose(): bool
    {
        return TIME > $this->data['deadlines'] ? true : false;
    }

    public function __set($name, $value)
    {
        // поля, при изменении которых будут обновлятся данные в БД
        $update_filds = ['deadlines', 'status', 'importance', 'message', 'id_project'];
        if (in_array($name, $update_filds)) {
            $this->_update = true;
        }
        switch ($name) {
            case 'deadlines':
                return $this->data['deadlines'] = $this->setDeadlines($value);
            case 'importance':
                return $this->data['importance'] = self::setImportance($value);
            default:
                return $this->data[$name] = $value;
        }
    }
    public function __get($name)
    {
        switch ($name) {
            case 'importance_str':
                return $this->getStrImportance();
            case 'time_create':
                return Misc::getTime($this->data['time_create']);
            case 'deadlines_form':
                return Misc::getTime($this->data['deadlines'], 'Y-m-d\TH:i');
            case 'deadlines':
                return Misc::getTime($this->data['deadlines']);
            case 'importance':
                return $this->getImportance();
            default:
                return $this->data[$name] ?? '';
        }
    }
    public function __isset($name)
    {
        switch ($name) {
            case 'deadlines_form':
                return true;
            case 'importance_str':
                return true;

            default:
                return isset($this->data[$name]);
        }
    }
    # обновляем данные в БД
    private function update()
    {
        if (!$this->_update) {
            return;
        }
        $q = DB::me()->prepare("UPDATE `tasks` SET `message` = :message, `id_project` = :id_project, `deadlines` = :deadlines, `status` = :status, `importance` = :importance WHERE `id` = :id LIMIT 1");
        $q->bindParam(':message', $this->data['message'], \PDO::PARAM_STR);
        $q->bindParam(':id_project', $this->data['id_project'], \PDO::PARAM_INT);
        $q->bindParam(':deadlines', $this->data['deadlines'], \PDO::PARAM_INT);
        $q->bindParam(':status', $this->data['status'], \PDO::PARAM_INT);
        $q->bindParam(':importance', $this->data['importance'], \PDO::PARAM_INT);
        $q->bindParam(':id', $this->data['id'], \PDO::PARAM_INT);
        $q->execute();
        $this->_update = false;
    }
    # удаление задачи
    public function delete()
    {
        $q = DB::me()->prepare("DELETE FROM `tasks` WHERE `id` = :id LIMIT 1");
        $q->bindParam(':id', $this->data['id'], \PDO::PARAM_INT);
        $q->execute();
    }
   public function __destruct()
   {
       $this->update();
   }
}
