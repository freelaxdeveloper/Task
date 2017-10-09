<?php

namespace App\Http\Controllers;

use \App\Core\{Controller,Authorize,Captcha,Form,App};
use \App\Models\Users;
use \Libraries\More\Text;

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
                    $this->params['errors'][] = __('Не верно введен проверочный код');
                } elseif ($user = Users::getUserByPassword($login, $password)) {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = __('Вы успешно авторизованы');
                    unset($_SESSION['count_error_authorize']);
                    header('Refresh: 1; /');
                } else {
                    $this->params['errors'][] = __('Вы ошиблись при вводе логина или пароля');

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
        $form->input(['name' => 'login',    'title' => __('Логин'),  'holder' => __('Введите логин')]);
        $form->input(['name' => 'password', 'title' => __('Пароль'), 'holder' => __('Введите пароль'), 'type' => 'password']);
        $form->submit(['name' => 'authorize', 'value' => __('Войти')]);
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
                $this->params['errors'][] = __('Не верно введен проверочный код');
            }
            if (!$login) {
                $this->params['errors'][] = __('Введите логин');
            }
            if (!$password || $password != $password1) {
                $this->params['errors'][] = __('Пароли не совпадают');
            }
            if (empty($this->params['errors'])) {
                $user = Users::addUser($login, $password);
                if (isset($user['error'])) {
                    $this->params['errors'][] = $user['error'];
                } else {
                    Authorize::authorized($user['id'], $user['password']);
                    $this->params['messages'][] = __('Вы успешно зарегистрировались');
                    header('Refresh: 1; /');
                }
            }
        }
        $form = new Form('/register/send/');
        $form->captcha = true;
        $form->input(['name' => 'login',    'title' => __('Логин'),  'holder' => __('Введите логин')]);
        $form->input(['name' => 'password', 'title' => __('Пароль'), 'holder' => __('Введите пароль'), 'type' => 'password']);
        $form->input(['name' => 'password1', 'title' => __('Пароль'), 'holder' => __('Повторите пароль'), 'type' => 'password']);
        $form->submit(['name' => 'register', 'value' => __('Регистрация')]);
        $this->params['form_register'] = $form->display();


        $this->display('main/register');
    }

}
