<?php
namespace Controllers;

use \Core\{Controller,Authorize,App};
use \Models\{Users};
use \More\Text;

class AuthorizeController extends Controller{
    # выход с профиля
    public function actionExit()
    {
        $this->access_user();

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
                    $this->params['messages'][] = 'Вы успешно авторизовались';
                    header('Refresh: 1; /');
                } else {
                    $this->params['errors'][] = 'Логин или пароль введены не верно';
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
                $this->params['errors'][] = 'Не правильный логин';
            }
            if (!$password || $password != $password1) {
                $this->params['errors'][] = 'Не правильный пароль, или пароли не совпадают';
            }
            if (empty($this->params['errors'])) {
                if (!Users::addUser($login, $password)) {
                    $this->params['errors'][] = 'Пользователь с таким логином уже существует';
                } else {
                    $this->params['messages'][] = 'Вы успешно зарегистрировались';
                }
            }
        }
        $this->display('main/register');
    }

}
