<?php
namespace Models;

use \Core\DB;

class User{
    private $id;
    private const TIME_UPDATE_TOKEN = 1800;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->data = $this->getData();
    }
    private function getData(): array
    {
        $q = DB::me()->prepare("SELECT `users`.*,
            (SELECT COUNT(*) FROM `tasks` WHERE `id_user` = `users`.`id`) AS 'task_count',
            (SELECT COUNT(*) FROM `projects` WHERE `id_user` = `users`.`id`) AS 'project_count'
            FROM `users`
            WHERE `id` = :id LIMIT 1");
        $q->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $q->execute();
        if ($user = $q->fetch()) {
            return $user;
        }
        $this->id = 0;
        return ['id' => 0, 'login' => '[Guest]', 'group' => 0];
    }
    public function __get($name)
    {
        return $this->data[$name] ?? '';
    }
    public function __isset($name): bool
    {
        return isset($this->data[$name]) ? true : false;
    }
    # проверяем токен
    public function checkToken(): bool
    {
        if (!isset($_GET['token']) && !isset($_POST['token'])) {
            return false;
        }
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if ($token == $this->data['url_token']) {
            return true;
        }
        return false;
    }
    # переодически обновляем токен
    private function setToken()
    {
        if (!$this->id || $this->data['token_time_update'] > TIME) {
            return;
        }
        $q = DB::me()->prepare("UPDATE `users` SET `token_time_update` = ?, `url_token` = ? WHERE `id` = ? LIMIT 1");
        $q->execute([TIME + self::TIME_UPDATE_TOKEN, bin2hex(random_bytes(32)), $this->id]);
    }
    public function delete()
    {
        $q = DB::me()->prepare("DELETE FROM `tasks` WHERE `id_user` = ?");
        $q->execute([$this->id]);

        $q = DB::me()->prepare("DELETE FROM `projects` WHERE `id_user` = ?");
        $q->execute([$this->id]);

        $q = DB::me()->prepare("DELETE FROM `users` WHERE `id` = ? LIMIT 1");
        $q->execute([$this->id]);
    }
    public function __destruct()
    {
        $this->setToken();
    }
}
