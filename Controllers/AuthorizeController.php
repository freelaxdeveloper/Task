<?php

namespace Controllers;

use \Core\{Controller,Authorize,App};
use \Models\{Users,Captcha,Form};
use \More\Text;

class AuthorizeController extends Controller{
    # количество не верный попыток авторизаций, после которых включается капча
    const COUNT_ERROR_CAPTCHA = 3;
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
                if (isset($_SESSION['count_error_authorize']) && $_SESSION['count_error_authorize'] >= self::COUNT_ERROR_CAPTCHA && !Captcha::check()) {
                    $this->params['errors'][] = 'Не верно введен проверочный код';
                } elseif ($user = Users::getUserByPassword($login, $password)) {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = 'Вы успешно авторизованы';
                    unset($_SESSION['count_error_authorize']);
                    header('Refresh: 1; /');
                } else {
                    $this->params['errors'][] = 'Вы ошиблись при вводе логина или пароля';

                    if (!isset($_SESSION['count_error_authorize'])) {
                        $_SESSION['count_error_authorize'] = 1;
                    } else {
                        ++$_SESSION['count_error_authorize'];
                    }
                }
            }
        }
        $form = new Form('/authorize/send/');
        if (isset($_SESSION['count_error_authorize']) && $_SESSION['count_error_authorize'] >= self::COUNT_ERROR_CAPTCHA) {
            $form->captcha = true;
        }
        $form->input(['name' => 'login',    'title' => 'Логин',  'holder' => 'Введите логин']);
        $form->input(['name' => 'password', 'title' => 'Пароль', 'holder' => 'Введите пароль', 'type' => 'password']);
        $form->submit(['name' => 'authorize', 'value' => 'Войти']);
        $this->params['form_authorize'] = $form->display();

        $this->display('main/authorize');
    }
    public function actionRegister()
    {
        $this->access_guest();

        if (isset($_POST['register'])) {
            $login = Text::for_name($_POST['login']);
            $password = Text::input_text($_POST['password']);
            $password1 = Text::input_text($_POST['password1']);

            if (!Captcha::check()) {
                $this->params['errors'][] = 'Не верно введен проверочный код';
            }
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
        $form = new Form('/register/send/');
        $form->captcha = true;
        $form->input(['name' => 'login',    'title' => 'Логин',  'holder' => 'Введите логин']);
        $form->input(['name' => 'password', 'title' => 'Пароль', 'holder' => 'Введите пароль', 'type' => 'password']);
        $form->input(['name' => 'password1', 'title' => 'Пароль', 'holder' => 'Повторите пароль', 'type' => 'password']);
        $form->submit(['name' => 'register', 'value' => 'Регистрация']);
        $this->params['form_register'] = $form->display();


        $this->display('main/register');
    }

}
