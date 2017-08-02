<?php
namespace Controllers;

use \Core\{Controller,Authorize,App};
use \More\Text;
use \Models\{Task};

class MainController extends Controller{

    public function actionIndex()
    {
        $this->params['tasks'] = Task::getAll();

        $this->display('main/index');
    }
}
