<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Task};
use \More\{Text,Misc};

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

        if (isset($_POST['message']) && isset($_POST['deadlines']) && isset($_POST['color']) && isset($_POST['id_project'])) {
            # задание
            $message = Text::input_text($_POST['message']);
            # дата, когда нужно выполнить задание
            $deadlines = Text::input_text($_POST['deadlines']);
            # важность задания
            $importance = Task::getImportance($_POST['color']);
            # ID проекта
            $id_project = (int) abs($_POST['id_project']);

            if ($message && $deadlines && Misc::validateDate($deadlines) && $id_project) {
                # хранить дату будем в UNIX
                $date = new \DateTime($deadlines);
                $deadlines = $date->format('U');

                Task::create($message, $deadlines, $importance, $id_project);
                header('Location: ' . App::referer());
            }
        }
    }
    # завершение задания
    public function actionComplete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        Task::setComplete($id_task);
        header('Location: ' . App::referer());
    }
    # просмотр завершенных заданий
    public function actionViewComplete(int $id_project)
    {
        echo 'hello';
    }
}
