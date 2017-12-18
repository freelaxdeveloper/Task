<?php
namespace App\Models;

use \App\Core\DB;
use Illuminate\Database\Eloquent\Model;

class User extends Model{
    const TIME_UPDATE_TOKEN = 1800;

    # проверяем токен
    public function checkToken(): bool
    {
        if (!$this->id) {
            return false;
        }
        if (!isset($_GET['token']) && !isset($_POST['token'])) {
            return false;
        }
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if ($token == $this->url_token) {
            $this->updateToken();
            return true;
        }
        return false;
    }
    # обновляем токен
    public function updateToken()
    {
        $hash = bin2hex(random_bytes(32));

        $this->url_token = $hash;
        $this->token_ip = ip2long($_SERVER['REMOTE_ADDR']);
        $this->token_time_update = TIME + self::TIME_UPDATE_TOKEN;
        return $this->save();
    }
}
