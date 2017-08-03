<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Project,Task};
use \More\Text;

class ProjectController extends Controller{

    public function actionView(int $id_project)
    {
        $project = Project::getOne($id_project);
        if (!$project) {
            $this->access_denied('Проект не найден');
        }
        # получем список заданий
        $tasks = Task::getByProject($id_project);

        $this->params['title'] = $project['title'];
        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;

        $this->display('project/view');
    }
    # просмотр завершенных заданий
    public function actionViewComplete(int $id_project)
    {
        $project = Project::getOne($id_project);

        if (!$project) {
            $project = ['title' => 'Весь список', 'id' => 0];
            $tasks = Task::getAll(2);
        } else {
            $tasks = Task::getByProject($id_project, 2);
        }

        $this->params['title'] = $project['title'] . ' - выполненные задания';
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
                Project::create($title, $color);
            }
        }
        header('Location: ' . App::referer());
    }
    # удаляем проект
    public function actionDelete(int $id_project)
    {
        $this->access_user(); # доступ только авторизированным
        if (Project::deleteOne($id_project)) {
            header('Location: ' . App::referer());
        } else {
            $this->params['title'] = 'Ошибка удаления';
            $this->access_denied('Проект не может быть удален пока в нем есть не выполненные задания');
        }
    }
}
