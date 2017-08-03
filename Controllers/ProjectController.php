<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Project,Task};

class ProjectController extends Controller{

    public function actionView(int $id_project)
    {
        # получем список заданий
        if (!$tasks = Task::getByProject($id_project)) {
            # если заданий нету получем данные проекта (если задания есть то данные проекта там присутствуют)
            $project = Project::getOne($id_project);
        }
        $title = $tasks[0]['title'] ?? $project['title'] ?? false;

        if (!$title) {
            $this->access_denied('Проект не найден');
        }

        $this->params['title'] = $title;
        $this->params['tasks'] = $tasks;

        $this->display('project/view');
    }
    # удаляем проект
    public function actionDelete(int $id_project)
    {
        Project::deleteOne($id_project);
        header('Location: ' . App::referer());
    }
}
