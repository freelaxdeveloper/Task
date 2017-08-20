<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Tasks,Task,Project};
use \More\{Text,Misc};

class TaskController extends Controller{

    public function actionDelete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным
        $this->checkToken(); # доступ только по токену

        if (!$tasks = Tasks::getTasks(['id' => $id_task])) {
            $tasks = Tasks::getTasks(['id' => $id_task, 'status' => 2]);
        }
        $task = new Task($tasks);

        if (!$task->id) {
            $this->access_denied('Задача не найдена');
        }

        $project = new Project($task->id_project);
        # недостаточно прав для удаления, (можно только автору задачи или владельцу проекта)
        if ($task->id_user != App::user()->id && $project->id_user != App::user()->id) {
            $this->access_denied('У вас не достаточно прав');
        }
        $task->delete();
        header('Location: ' . App::referer());
    }
    # добавляем задание
    public function actionCreate()
    {
        $this->access_user(); # доступ только авторизированным
        $this->checkToken(); # доступ только по токену

        if (isset($_POST['message']) && isset($_POST['deadlines']) && isset($_POST['color']) && isset($_POST['id_project'])) {
            # задание
            $message = Text::input_text($_POST['message']);
            # дата, когда нужно выполнить задание
            $deadlines = Text::input_text($_POST['deadlines']);
            # важность задания
            $importance = Task::setImportance($_POST['color']);
            # ID проекта
            $id_project = (int) abs($_POST['id_project']);

            $project = new Project($id_project);
            # недостаточно прав для добавления задания (зависит от настройки проекта)
            if (!$project->management()) {
                $this->access_denied('У вас не достаточно прав');
            }
            if ($message && $deadlines && $project->id) {
                # хранить дату будем в UNIX
                $date = new \DateTime($deadlines);
                if ($deadlines = $date->getTimestamp()) {
                    Tasks::create($message, $deadlines, $importance, $project->id);
                }
            }
        }
        header('Location: ' . App::referer());
    }
    # завершение задания
    public function actionComplete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным
        $this->checkToken(); # доступ только по токену

        $tasks = Tasks::getTasks(['id' => $id_task]);
        $task =  new Task($tasks);
        if (!$task->id) {
            $this->access_denied('Задача не найдена');
        }
        $project = new Project($task->id_project);

        # недостаточно прав для выполнения (зависит от настройки проекта)
        if (!$project->management()) {
            $this->access_denied('У вас не достаточно прав');
        }
        $task->status = 2;
        header('Location: ' . App::referer());
    }
    # редактирование задания
    public function actionEdit(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $tasks = Tasks::getTasks(['id' => $id_task]);
        $task = new Task($tasks);

        # задачи не существует
        if (!$task->id) {
            $this->access_denied('Задача не найдена');
        }

        $project = new Project($task->id_project);
        # задача уже выполена, не будем её больше трогать
        if ($task->status == 2) {
            $this->access_denied('Выполненную задачу редактировать нельзя');
        }
        # недостаточно прав для редактирования (можно автору задачи или владельцу проекта)
        if ($task->id_user != App::user()->id && $project->id_user != App::user()->id) {
            $this->access_denied('У вас не достаточно прав');
        }
        # проверяем можно ли пользователю вести проект

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
