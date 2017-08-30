<?php

namespace Controllers;

use \Core\{Controller,App};
use \Models\{User,Form,Captcha};

class UserController extends Controller{
    public function actionDelete(int $id_user)
    {
        # доступ только администратору
        if (App::user()->group < App::USER_GROUP_ADMIN || App::user()->id == $id_user) {
            $this->access_denied('Доступ закрыт');
        }
        $user = new User($id_user);

        if (!$user->id) {
            $this->access_denied('Пользователь не найден');
        }
        if (isset($_POST['delete'])) {
            $this->checkToken(); # доступ только по токену
            $task_count = (int) $_POST['task_count'];

            if (!Captcha::check()) {
                $this->params['errors'][] = 'Не верно введен проверочный код';
            } elseif($task_count != $user->task_count) {
                $this->params['errors'][] = 'Не верное количество задач';
            } else {
                $user->delete();
                $this->params['messages'][] = 'Пользовательские данные успешно удалены';
                header('Refresh: 1; /' . App::referer());
            }
        }
        $this->params['title'] = $user->login . ' - удаление профиля';

        $form = new Form('/user/delete/' . $user->id . '/');
        $form->captcha = true;
        $form->input(['name' => 'token', 'value' => App::user()->url_token, 'type' => 'hidden', 'br' => false]);
        $form->input(['name' => 'task_count', 'title' => 'Сколько у пользователя добавлено задач?']);
        $form->html('Удалится вся информация связанная с данным профилем!');
        $form->submit(['name' => 'delete', 'value' => 'Подтверждаю удаление']);
        $this->params['form_delete_user'] = $form->display();

        $this->display('user/delete');
    }
}
