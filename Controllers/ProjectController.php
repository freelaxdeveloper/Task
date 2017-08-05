<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Projects,Tasks};
use \More\Text;

class ProjectController extends Controller{

    public function actionView(int $id_project)
    {
        $project = Projects::getOne($id_project);
        if (!$project) {
            $this->access_denied('Проект не найден');
        }
        # получем список заданий
        $tasks = Tasks::getTasks(['id_project' => $project['id']]);

        $this->params['title'] = $project['title'];
        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;
        $this->params['id_activePproject'] = $project['id'];

        $this->display('project/view');
    }
    public function actionEdit(int $id_project)
    {
        $project = Projects::getOne($id_project);
        if (!$project) {
            $this->access_denied('Проект не найден');
        }
        if (isset($_POST['title']) && isset($_POST['color_edit'])) {
            $title = Text::for_name($_POST['title']);
            $color = Text::for_name($_POST['color_edit']);

            if ($title && $color) {
                Projects::update($title, $color, $project['id']);
                header('Location: ' . App::referer());
            }
        }
        $this->params['title'] = $project['title'] . ' - редактирование';
        $this->params['project'] = $project;

        $this->display('project/edit');
    }
    # просмотр завершенных заданий
    public function actionViewComplete(int $id_project)
    {
        $project = Projects::getOne($id_project);

        if (!$project) {
            $project = ['title' => 'Весь список', 'id' => 0];
            $tasks = Tasks::getTasks(['status' => 2, 'time_start' => 0]);
            $this->params['title'] = 'Весь список выполненных заданий';
        } else {
            $tasks = Tasks::getTasks(['status' => 2, 'id_project' => $id_project, 'time_start' => 0]);
            $this->params['title'] = $project['title'] . ' - выполненные задания';
        }

        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;

        $this->display('project/view');
    }
    # добавляем проект
    public function actionCreate()
    {
        $this->access_user(); # доступ только авторизированным

        if (isset($_POST['title']) && isset($_POST['color'])) {
            $title = Text::for_name($_POST['title']);
            $color = Text::for_name($_POST['color']);

            if ($title && $color) {
                Projects::create($title, $color);
            }
        }
        header('Location: ' . App::referer());
    }
    # удаляем проект
    public function actionDelete(int $id_project)
    {
        $this->access_user(); # доступ только авторизированным
        if (Projects::deleteOne($id_project)) {
            header('Location: ' . App::referer());
        } else {
            $this->params['title'] = 'Ошибка удаления';
            $this->access_denied('Проект не может быть удален пока в нем есть не выполненные задания');
        }
    }
}
