<?php
namespace Controllers;

use Core\Controller;

class MainController extends Controller{

    public function actionIndex()
    {
        $this->display('main/index');
    }
}
