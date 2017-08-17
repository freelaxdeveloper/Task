<?php

namespace Controllers;

use \Core\{Controller,Authorize,App};
use \More\Text;
use \Models\{Tasks};

class MainController extends Controller{
    public function actionIndex()
    {
        $this->params['tasks'] = Tasks::getTasks();
        $this->params['id_activePproject'] = 0;
        //$this->params['sorting'] = 'today';

        $this->display('main/index');
    }
    public function actionLast(string $last, int $id_project)
    {
        switch ($last) {
            case 'week':
                $title = 'Задачи на неделю';
                $shit_days = 7;
                $sorting = 'week';
                break;
            case 'month':
                $title = 'Задачи на месяц';
                $shit_days = 30;
                $sorting = 'month';
                break;

            default:
                $title = 'Задачи на сегодня';
                $shit_days = 1;
                $sorting = 'today';
                break;
        }
        $params = [];
        $params['time_start'] = mktime(0, 0, 0);
        $params['shit_days'] = $shit_days;
        if ($id_project) {
            $params['id_project'] = $id_project;
            $this->params['id_activePproject'] = $id_project;
        } else {
            $this->params['id_activePproject'] = 0;
        }

        $this->params['title'] = $title;
        $this->params['sorting'] = $sorting;
        $this->params['tasks'] = Tasks::getTasks($params);
        $this->display('main/index');
    }
}
