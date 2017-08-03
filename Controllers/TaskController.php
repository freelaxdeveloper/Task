<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Task};

class TaskController extends Controller{

    public function actionDelete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        Task::deleteOne($id_task);
        header('Location: ' . App::referer());
    }
    # добавляем задание
    public function actionCreate()
    {
        $this->access_user(); # доступ только авторизированным

        if (isset($_POST['add'])) {

        }

        printr($_POST);
    }
}
