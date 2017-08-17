<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Projects,Tasks};
use \More\Text;

class ProjectController extends Controller{

    public function actionView(int $id_project, string $sorting = 'today')
    {
        switch ($sorting) {
            case 'week':
                $shit_days = 7;
                break;
            case 'month':
                $shit_days = 30;
                break;

            default:
                $shit_days = 1;
                break;
        }

        $project = Projects::getOne($id_project);
        if (!$project) {
            $this->access_denied('Project not found');
        }
        # получем список заданий
        $tasks = Tasks::getTasks(['id_project' => $project['id'], 'shit_days' => $shit_days]);

        $this->params['title'] = $project['title'];
        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;
        $this->params['id_activePproject'] = $project['id'];
        $this->params['sorting'] = $sorting;

        $this->display('project/view');
    }
    public function actionEdit(int $id_project)
    {
        $project = Projects::getOne($id_project);
        if (!$project) {
            $this->access_denied('Project not found');
        }
        # недостаточно прав для редактирования, (можно только автору)
        if ($project['id_user'] != App::user()->id) {
            $this->access_denied('You do not have enough authority');
        }

        if (isset($_POST['title']) && isset($_POST['color_edit'])) {
            $title = Text::for_name($_POST['title']);
            $color = Text::for_name($_POST['color_edit']);

            if ($title && $color) {
                Projects::update($title, $color, $project['id']);
                header('Location: ' . App::referer());
            }
        }
        $this->params['title'] = $project['title'] . ' - editing';
        $this->params['project'] = $project;

        $this->display('project/edit');
    }
    # просмотр завершенных заданий
    public function actionViewComplete(int $id_project)
    {
        $project = Projects::getOne($id_project);

        if (!$project) {
            $project = ['title' => 'The whole list', 'id' => 0];
            $tasks = Tasks::getTasks(['status' => 2, 'time_start' => 0]);
            $this->params['title'] = 'Complete list of completed tasks';
        } else {
            $tasks = Tasks::getTasks(['status' => 2, 'id_project' => $id_project, 'time_start' => 0]);
            $this->params['title'] = $project['title'] . ' - completed tasks';
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

        $project = Projects::getOne($id_project);
        if (!$project) {
            $this->access_denied('Project not found');
        }
        # недостаточно прав для удаления, (можно только автору)
        if ($project['id_user'] != App::user()->id) {
            $this->access_denied('You do not have enough authority');
        }

        if (Projects::deleteOne($project['id'])) {
            header('Location: ' . App::referer());
        } else { # если удалить не смогли значит там есть незавершенные задачи
            $this->params['title'] = 'Uninstall error';
            $this->access_denied('The project can not be deleted while there are uncompleted tasks in it');
        }
    }
}
