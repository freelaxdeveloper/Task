<?php
namespace Models;

use \Core\DB;

class Project{
    private $id;
    private $data;

    public function __construct(int $id_project)
    {
        $this->id = $id_project;
        $this->data = $this->getData();
    }
    # получение данных проекта
    private function getData()
    {
        $q = DB::me()->prepare("SELECT * FROM `projects` WHERE `id` = :id_project LIMIT 1");
        $q->bindParam(':id_project', $this->id, \PDO::PARAM_INT);
        $q->execute();
        if ($project = $q->fetch()) {
            return $project;
        }
        return ['title' => 'None', 'id' => 0];
    }

    public function __get($name)
    {
        return $this->data[$name] ?? '';
    }
    public function __isset($name)
    {
        return isset($this->data[$name]) ? true : false;
    }
}
