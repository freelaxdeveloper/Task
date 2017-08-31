<?php
namespace Models;

use \Core\DB;

class User{
    private $id;
    const TIME_UPDATE_TOKEN = 1800;

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
        if ($name == 'lang') {
            return 'en';
        }
        return $this->data[$name] ?? '';
    }
    public function __isset($name): bool
    {
        return isset($this->data[$name]) ? true : false;
    }
    # проверяем токен
    public function checkToken(): bool
    {
        if (!$this->data['id']) {
            return false;
        }
        if (!isset($_GET['token']) && !isset($_POST['token'])) {
            return false;
        }
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if ($token == $this->data['url_token']) {
            $this->updateToken();
            return true;
        }
        return false;
    }
    # переодически обновляем токен
    public function updateToken()
    {
        $hash = bin2hex(random_bytes(32));

        $q = DB::me()->prepare("UPDATE `users` SET `token_time_update` = ?, `url_token` = ?, `token_ip` = ? WHERE `id` = ? LIMIT 1");
        $q->execute([TIME + self::TIME_UPDATE_TOKEN, $hash, $this->getTokenSoil(), $this->id]);
    }
    private function getTokenSoil()
    {
        return ip2long($_SERVER['REMOTE_ADDR']);
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

    }
}
