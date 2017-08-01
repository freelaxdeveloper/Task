<?php
namespace Controllers;

use Core\Controller;
use \Models\{Project,Task};

class MainController extends Controller{

    public function actionIndex()
    {
        $projects = Project::getAll();
        $tasks = Task::getAll();

        $this->params['projects'] = $projects;
        $this->params['tasks'] = $tasks;

        $this->display('main/index');
    }
}
