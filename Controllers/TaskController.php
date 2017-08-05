<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Tasks,Task};
use \More\{Text,Misc};

class TaskController extends Controller{

    public function actionDelete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $task = new Task($id_task);

        if (!$task->id) {
            $this->access_denied('Задание не найдено');
        }
        $task->delete();
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
            $importance = Task::setImportance($_POST['color']);
            # ID проекта
            $id_project = (int) abs($_POST['id_project']);

            if ($message && $deadlines && $id_project) {
                # хранить дату будем в UNIX
                $date = new \DateTime($deadlines);
                if ($deadlines = $date->getTimestamp()) {
                    Tasks::create($message, $deadlines, $importance, $id_project);
                }
            }
        }
        header('Location: ' . App::referer());
    }
    # завершение задания
    public function actionComplete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $task = new Task($id_task);

        if (!$task->id) {
            $this->access_denied('Задание не найдено');
        }

        $task->status = 2;
        header('Location: ' . App::referer());
    }
    # редактирование задания
    public function actionEdit(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $task = new Task($id_task);
        
        if (!$task->id || $task->status == 2) {
            $this->access_denied('Задание не найдено');
        }

        if (isset($_POST['message']) && isset($_POST['deadlines']) && isset($_POST['color_edit']) && isset($_POST['id_project'])) {
            # задание
            $message = Text::input_text($_POST['message']);
            # дата, когда нужно выполнить задание
            $deadlines = Text::input_text($_POST['deadlines']);
            # важность задания
            $importance = Text::input_text($_POST['color_edit']);
            # ID проекта
            $id_project = (int) abs($_POST['id_project']);

            if ($message && $deadlines && $id_project && $importance) {
                $task->message = $message;
                $task->deadlines = $deadlines;
                $task->importance = $importance;
                $task->id_project = $id_project;
            }
        }

        $this->params['title'] = $task->message . ' - редактирование';
        $this->params['task'] = $task;
        $this->params['id_activePproject'] = $task->id_project;

        $this->display('task/edit');
    }
}
