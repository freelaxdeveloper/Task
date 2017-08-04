<?php
namespace Controllers;

use \Core\{Controller,Authorize,App};
use \More\Text;
use \Models\{Tasks};

class MainController extends Controller{

    public function actionIndex()
    {
        $this->params['tasks'] = Tasks::getAll();
        
        $this->display('main/index');
    }
}
