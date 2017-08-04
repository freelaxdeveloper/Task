<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Tasks};
use \More\{Text,Misc};

class TaskController extends Controller{

    public function actionDelete(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        Tasks::deleteOne($id_task);
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
            $importance = Tasks::getImportance($_POST['color']);
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

        Tasks::setComplete($id_task);
        header('Location: ' . App::referer());
    }
    # редактирование задания
    public function actionEdit(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $task = Tasks::getOne($id_task);

        if (!$task['id']) {
            $this->access_denied('Задание не найдено');
        }
        $date = new \DateTime();
        $date->setTimestamp($task['deadlines']);
        $task['deadlines'] = $date->format('Y-m-d\TH:i');

        $task['importance'] = Tasks::getImportanceStr($task['importance']);

        $this->params['title'] = $task['message'] . ' - редактирование';
        $this->params['task'] = $task;

        $this->display('task/edit');
    }
    # редактирование задания сохранение
    public function actionEditSave(int $id_task)
    {
        $this->access_user(); # доступ только авторизированным

        $task = Tasks::getOne($id_task);

        if (!$task['id']) {
            $this->access_denied('Задание не найдено');
        }
        if (isset($_POST['message']) && isset($_POST['deadlines']) && isset($_POST['color_edit']) && isset($_POST['id_project'])) {
            # задание
            $message = Text::input_text($_POST['message']);
            # дата, когда нужно выполнить задание
            $deadlines = Text::input_text($_POST['deadlines']);
            # важность задания
            $importance = Tasks::getImportance($_POST['color_edit']);
            # ID проекта
            $id_project = (int) abs($_POST['id_project']);

            if ($message && $deadlines && $id_project) {
                # хранить дату будем в UNIX
                $date = new \DateTime($deadlines);
                if ($deadlines = $date->format('U')) {
                    Tasks::update($message, $deadlines, $importance, $id_project, $task['id']);
                }
            }
        }
        header('Location: /task/edit/' . $task['id'] . '/');
    }
}
