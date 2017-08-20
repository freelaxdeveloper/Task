<?php
namespace Controllers;

use \Core\{Controller,App};
use \Models\{Project,Projects,Tasks};
use \More\Text;

class ProjectController extends Controller{

    public function actionView(int $id_project, string $sorting = 'all')
    {
        switch ($sorting) {
            case 'week':
                $shit_days = 7;
                $time_start = mktime(0, 0, 0);
                break;
            case 'month':
                $shit_days = 30;
                $time_start = mktime(0, 0, 0);
                break;
            case 'today':
                $shit_days = 1;
                $time_start = mktime(0, 0, 0);
                break;

            default:
                $shit_days = 1;
                $time_start = 0;
        }

        $project = new Project($id_project);
        if (!$project->id) {
            $this->access_denied('Проект не найден');
        }
        # получем список заданий
        $tasks = Tasks::getTasks(['id_project' => $project->id, 'shit_days' => $shit_days, 'time_start' => $time_start]);

        $this->params['title'] = $project->title;
        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;
        $this->params['id_activePproject'] = $project->id;
        $this->params['sorting'] = $sorting;

        $this->display('project/view');
    }
    public function actionEdit(int $id_project)
    {
        $project = new Project($id_project);
        if (!$project->id) {
            $this->access_denied('Проект не найден');
        }
        # недостаточно прав для редактирования, (можно только автору)
        if (!$project->management()) {
            $this->access_denied('У вас не достаточно прав');
        }

        if (isset($_POST['title']) && isset($_POST['color_edit'])) {
            $title = Text::for_name($_POST['title']);
            $color = Text::for_name($_POST['color_edit']);
            $set_management = $_POST['set_management'] == 2 ? 2 : 1;

            if ($title && $color) {
                Projects::update($title, $color, $set_management, $project->id);
                header('Location: ' . App::referer());
            }
        }
        $this->params['title'] = $project->title . ' - редактирование';
        $this->params['project'] = $project;

        $this->display('project/edit');
    }
    # просмотр завершенных заданий
    public function actionViewComplete(int $id_project)
    {
        $project = new Project($id_project);

        if (!$project->id) {
            $project = ['title' => 'Весь список', 'id' => 0];
            $tasks = Tasks::getTasks(['status' => 2, 'time_start' => 0]);
            $this->params['title'] = 'Список выполненных задач';
        } else {
            $tasks = Tasks::getTasks(['status' => 2, 'id_project' => $id_project, 'time_start' => 0]);
            $this->params['title'] = $project->title . ' - выполненные задачи';
        }

        $this->params['tasks'] = $tasks;
        $this->params['project'] = $project;

        $this->display('project/view');
    }
    # добавляем проект
    public function actionCreate()
    {
        $this->access_user(); # доступ только авторизированным
        $this->checkToken(); # доступ только по токену
        
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
        $this->checkToken(); # доступ только по токену

        $project = new Project($id_project);
        if (!$project->id) {
            $this->access_denied('Проект не найден');
        }
        # недостаточно прав для удаления, (можно только автору)
        if (!$project->management()) {
            $this->access_denied('У вас не достаточно прав');
        }

        if ($project->delete()) {
            header('Location: ' . App::referer());
        } else { # если удалить не смогли значит там есть незавершенные задачи
            $this->params['title'] = 'Ошибка при удалении';
            $this->access_denied('Проект содержит невыполненные задачи');
        }
    }
}
