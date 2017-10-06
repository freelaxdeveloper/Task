<?php
namespace App\Http\Controllers;

use \Core\{Controller,Authorize,App,Captcha};
use \More\Text;
use \Models\{Tasks};

class MainController extends Controller{

    public function __construct()
    {
        $this->params['add_task'] = true; // показываем форму добавления задачи
    }

    public function actionIndex()
    {
        $this->params['tasks'] = Tasks::getTasks(['my_task' => true, 'status' => 1]);

        $this->params['id_activePproject'] = 0;
        //$this->params['sorting'] = 'today';

        $this->display('main/index');
    }
    public function actionFaq()
    {
        $this->params['add_task'] = false; // не показываем форму добавления задачи
        $this->display('main/faq');
    }
    public function actionLast(string $last, int $id_project)
    {
        switch ($last) {
            case 'week':
                $title = __('Задачи на неделю');
                $shit_days = 7;
                $sorting = 'week';
                break;
            case 'month':
                $title = __('Задачи на месяц');
                $shit_days = 30;
                $sorting = 'month';
                break;

            default:
                $title = __('Задачи на сегодня');
                $shit_days = 1;
                $sorting = 'today';
                break;
        }
        $params = [];
        $params['shit_days'] = $shit_days;
        $params['status'] = 1;
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
