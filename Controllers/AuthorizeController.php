<?php

namespace Controllers;

use \Core\{Controller,Authorize,App};
use \Models\{Users};
use \More\Text;

class AuthorizeController extends Controller{
    # выход с профиля
    public function actionExit()
    {
        $this->access_user(); # доступ только авторизированным
        $this->checkToken(); # доступ только по токену

        Authorize::exit();
        header('Location: /');
    }
    public function actionAuthorize()
    {
        $this->access_guest();
        if (isset($_POST['authorize'])) {
            $login = Text::for_name($_POST['login']);
            $password = Text::input_text($_POST['password']);
            if ($login && $password) {
                if ($user = Users::getUserByPassword($login, $password)) {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = 'Вы успешно авторизованы';
                    header('Refresh: 1; /');
                } else {
                    $this->params['errors'][] = 'Вы ошиблись при вводе логина или пароля';
                }
            }
        }
        $this->display('main/authorize');
    }
    public function actionRegister()
    {
        $this->access_guest();

        if (isset($_POST['register'])) {
            $login = Text::for_name($_POST['login']);
            $password = Text::input_text($_POST['password']);
            $password1 = Text::input_text($_POST['password1']);

            if (!$login) {
                $this->params['errors'][] = 'Введите логин';
            }
            if (!$password || $password != $password1) {
                $this->params['errors'][] = 'Пароли не совпадают';
            }
            if (empty($this->params['errors'])) {
                $user = Users::addUser($login, $password);
                if (isset($user['error'])) {
                    $this->params['errors'][] = $user['error'];
                } else {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = 'Вы успешно зарегистрировались';
                    header('Refresh: 1; /');
                }
            }
        }
        $this->display('main/register');
    }

}
