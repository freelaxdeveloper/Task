<?php
namespace Controllers;

use \Core\{Controller,Authorize,App};
use \More\Text;
use \Models\{Tasks};

class MainController extends Controller{

    public function actionIndex()
    {
        $this->params['tasks'] = Tasks::getTasks();
        $this->params['current_data'] = date('Y-m-d\TH:00');
        $this->params['id_activePproject'] = 0;

        $this->display('main/index');
    }
    public function actionLast(string $last, int $id_project)
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
        $params = [];
        $params['shit_days'] = $shit_days;
        if ($id_project) {
            $params['id_project'] = $id_project;
            $this->params['id_activePproject'] = $id_project;
        } else {
            $this->params['id_activePproject'] = 0;
        }

        $this->params['title'] = $title;
        $this->params['tasks'] = Tasks::getTasks($params);
        $this->display('main/index');
    }
}
