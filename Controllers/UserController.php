<?php

namespace Controllers;

use \Core\{Controller,App};
use \Models\{User};

class UserController extends Controller{
    public function actionDelete(int $id_user)
    {
        $this->checkToken(); # доступ только по токену

        # доступ только администратору
        if (App::user()->group < App::USER_GROUP_ADMIN || App::user()->id == $id_user) {
            $this->access_denied('Доступ закрыт');
        }
        $user = new User($id_user);

        if (!$user->id) {
            $this->access_denied('Пользователь не найден');
        }
        $user->delete();
        header('Location: ' . App::referer());
    }
}
