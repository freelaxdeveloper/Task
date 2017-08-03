<?php
namespace Controllers;

use \Core\{Controller};
use \Models\{Task};

class TaskController extends Controller{

    public function actionDelete(int $id_task)
    {
        Task::deleteOne($id_task);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
