<?php
namespace Core;

use \More\{Text,Pages};
use \Core\{App};
use \Models\{Projects,Users,Form};

class Controller{
    protected $params = [];
    protected $template_dir = 'default';

    protected function access_denied(string $msg)
    {
        $this->params['message'] = $msg;
        $this->display('access_denied');
        exit;
    }
    protected function display(string $filename)
    {
        $this->_inicialization();

        $this->params['server_name'] = $_SERVER['SERVER_NAME'];

        $loader = new \Twig_Loader_Filesystem(H . '/Views/' . $this->template_dir);
        $twig = new \Twig_Environment($loader);

        $template = $twig->loadTemplate('/' . $filename . '.twig');
        echo $template->render($this->params);
    }
    private function _inicialization()
    {
        $this->params['projects'] = Projects::getAll();
        $this->params['user'] = App::user();
        $this->params['users'] = Users::getAll();
        $this->params['current_data'] = date('Y-m-d\TH:00');
        if (empty($this->params['id_activePproject']))
            $this->params['id_activePproject'] = 0;

        if (App::user()->id) {
            $form = new Form('/project/new/');
            $form->html('<span id="typeProject"></span>');
            $form->input(['name' => 'token', 'value' => App::user()->url_token, 'type' => 'hidden', 'br' => false]);
            $form->input(['name' => 'color', 'value' => 'red', 'type' => 'hidden', 'br' => false]);
            $form->input(['name' => 'title', 'holder' => 'Введите название']);
            $form->submit(['name' => 'add', 'value' => 'Добавить', 'br' => false]);
            $form->submit(['name' => 'cancel', 'value' => 'Отмена', 'class' => 'cancel']);
            $this->params['form_project'] = $form->display();
        }
    }
    # доступ только пользователю
    protected function access_user()
    {
        if (!App::user()->id) {
            $this->access_denied('Страница доступна только авторизированным пользователям');
        }
    }
    # доступ только гостью
    protected function access_guest()
    {
        if (App::user()->id) {
            $this->access_denied('Страница доступна только гостью');
        }
    }
    # доступ только по токену
    protected function checkToken()
    {
        if (!App::user()->checkToken()) {
            $this->access_denied('Не верный token');
        }
    }
}
