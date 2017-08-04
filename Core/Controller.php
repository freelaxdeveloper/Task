<?php
namespace Core;

use \More\{Text,Pages};
use \Core\{App};
use \Models\{Projects};

class Controller{
    protected $params = [];
    protected $template_dir = 'default';

    public function __construct()
    {
        # если по какой либо причине константа не опредена
        # например этот файл не был открыв в корневом index.php
        # то ничего не должно работать, защита 80lvl =)
        if (!defined('TASK_ACCESS')) {
            App::access_denied('ACCESS DENIED');
        }
    }

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
    }
    # доступ только пользователю
    protected function access_user()
    {
        if (!App::user()->id) {
            $this->access_denied('Для просмотра страницы необходимо авторизоваться');
        }
    }
    # доступ только гостью
    protected function access_guest()
    {
        if (App::user()->id) {
            $this->access_denied('Страница доступна только гостью');
        }
    }
}
