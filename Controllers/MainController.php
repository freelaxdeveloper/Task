<?php
namespace Controllers;

use Core\Controller;
use More\Text;
use \Models\Users;

class MainController extends Controller{

    public function actionIndex()
    {
        $this->display('main/index');
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
