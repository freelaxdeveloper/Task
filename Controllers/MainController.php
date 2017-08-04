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
    public function actionLast(string $last)
    {
        switch ($last) {
            case 'week':
                $title = 'Задания на неделю';
                $shit_days = 7;
                break;
            case 'month':
                $title = 'Задания на месяц';
                $shit_days = 30;
                break;

            default:
                $title = 'Задания на сегодня';
                $shit_days = 1;
                break;
        }
        $this->params['title'] = $title;
        $this->params['tasks'] = Tasks::getAllForTime($shit_days);
        $this->display('main/index');
    }
}
