<?php
namespace Controllers;

use Core\{Controller,Authorize,App};
use More\Text;
use \Models\{Users};

class MainController extends Controller{

    public function actionIndex()
    {
        if (App::user()->id) {
            echo 'Вы авторизированы<br />';
        }
        $this->display('main/index');
    }

    public function actionAuthorize()
    {
        if (isset($_POST['authorize'])) {
            $login = Text::for_name($_POST['login']);
            $password = Text::input_text($_POST['password']);
            if ($login && $password) {
                if ($user = Users::getUserByPassword($login, $password)) {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = 'Вы успешно авторизовались';
                } else {
                    $this->params['errors'][] = 'Логин или пароль введены не верно';
                }
            }
        }
        $this->display('main/authorize');
    }
    public function actionRegister()
    {
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
