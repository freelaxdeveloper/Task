<?php
namespace Models;

use \Core\DB;

class User{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->data = $this->getData();
    }
    private function getData(): array
    {
        $q = DB::me()->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $q->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $q->execute();
        if ($user = $q->fetch()) {
            return $user;
        }
        return ['id' => 0, 'login' => 'Sanek'];
    }
    public function __get($name)
    {
        return $this->data[$name] ?? '';
    }
    public function __isset($name): bool
    {
        return isset($this->data[$name]) ? true : false;
    }
}
