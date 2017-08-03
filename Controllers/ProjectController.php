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

        Project::deleteOne($id_project);
        header('Location: ' . App::referer());
    }
}
