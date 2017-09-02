<?php

namespace Controllers;

use \Core\{Controller,App,Form,Captcha};
use \Models\{User};

class UserController extends Controller{
    public function actionDelete(int $id_user)
    {
        # доступ только администратору
        if (App::user()->group < App::USER_GROUP_ADMIN || App::user()->id == $id_user) {
            $this->access_denied(__('Доступ закрыт'));
        }
        $user = new User($id_user);

        if (!$user->id) {
            $this->access_denied(__('Пользователь не найден'));
        }
        if (isset($_POST['delete'])) {
            $this->checkToken(); # доступ только по токену
            $task_count = (int) $_POST['task_count'];

            if (!Captcha::check()) {
                $this->params['errors'][] = __('Не верно введен проверочный код');
            } elseif($task_count != $user->task_count) {
                $this->params['errors'][] = __('Не верное количество задач');
            } else {
                $user->delete();
                $this->params['messages'][] = __('Пользовательские данные успешно удалены');
                header('Refresh: 1; /' . App::referer());
            }
        }
        $this->params['title'] = __('%s - удаление профиля', $user->login);

        $form = new Form('/user/delete/' . $user->id . '/');
        $form->captcha = true;
        $form->input(['name' => 'token', 'value' => App::user()->url_token, 'type' => 'hidden', 'br' => false]);
        $form->input(['name' => 'task_count', 'title' => __('Сколько у пользователя добавлено задач')]);
        $form->html(__('Удалится вся информация связанная с данным профилем'));
        $form->submit(['name' => 'delete', 'value' => __('Подтверждаю удаление')]);
        $this->params['form_delete_user'] = $form->display();

        $this->display('user/delete');
    }
}
