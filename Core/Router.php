<?php
namespace Core;

use \Core\App;

class Router
{
    private $routes = [];

    public function __construct()
    {
        $this->routes = include_once(H . '/System/config/routes.php');
    }
    private function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }
    public function run()
    {
        foreach ($this->routes AS $path) {
            # сравниваем метод передачи данных
            if ($_SERVER['REQUEST_METHOD'] != $path['method']) {
                continue;
            }
            # ищем подходящий роут подходящий согласно правилу паттерна
            if (preg_match('~^' . $path['pattern'] . '$~', $this->getURI())) {
                # получаем внутренний путь из внешнего согласно правилу
                $internalRoute = preg_replace("~" . $path['pattern'] . "~", $path['run'], $this->getURI());

                # разбиваем переданные параметры на сегменты
                $segments = explode('/', $internalRoute);
                # определяем какoй контроллер обрабатывает запрос
                $controllName = '\Controllers\\' . ucfirst(array_shift($segments)) . 'Controller';
                # определяем какoй method обрабатывает запрос
                $actionName = 'action' . ucfirst(array_shift($segments));

                # подключаем файл класса-контроллера если он есть
                $controllerFile = str_replace('\\', '/', H . $controllName . '.php');
                if (!file_exists($controllerFile)) {
                    $this->access_denied('Missing file: ' . $controllerFile);
                }
                require_once $controllerFile;
                # проверяем существует ли класс который прописан в роутах
                if (!class_exists($controllName)) {
                    $this->access_denied('Missing class: ' . $controllName);
                }
                # проверяем существует ли метод в этом классе который прописан в роутах
                if (!method_exists($controllName, $actionName)) {
                    $this->access_denied('Missing method: ' . $actionName . ' in class ' . $controllName);
                }
                # если все ок - запускаем
                $controllObject = new $controllName;
                $result = call_user_func_array([$controllObject, $actionName], $segments);
                break;
            }
        }
        if (!isset($controllObject)) {
            $this->access_denied('You can not find a suitable page router: ' . $this->getURI());
        }
    }
    private function access_denied(string $error)
    {
        return App::access_denied($error);
    }
}
