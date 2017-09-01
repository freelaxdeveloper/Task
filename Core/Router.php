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
    public function run()
    {
        foreach ($this->routes AS $path) {
            # сравниваем метод передачи данных
            if (strpos($path['method'], $_SERVER['REQUEST_METHOD']) === false) {
                continue;
            }
            # ищем подходящий роут подходящий согласно правилу паттерна
            if (preg_match('~^' . $path['pattern'] . '$~', App::getURI())) {
                # получаем внутренний путь из внешнего согласно правилу
                $internalRoute = preg_replace("~" . $path['pattern'] . "~", $path['run'], App::getURI());

                # разбиваем переданные параметры на сегменты
                $segments = explode('/', $internalRoute);
                # определяем какoй контроллер обрабатывает запрос
                $controllName = '\Controllers\\' . ucfirst(array_shift($segments)) . 'Controller';
                # определяем какoй method обрабатывает запрос
                $actionName = 'action' . ucfirst(array_shift($segments));

                # подключаем файл класса-контроллера если он есть
                $controllerFile = str_replace('\\', '/', H . $controllName . '.php');
                if (!file_exists($controllerFile)) {
                    $this->access_denied(__('Отсувствует файл: %s', $controllerFile));
                }
                require_once $controllerFile;
                # проверяем существует ли класс который прописан в роутах
                if (!class_exists($controllName)) {
                    $this->access_denied(__('Отсувствует класс: %s', $controllName));
                }
                # проверяем существует ли метод в этом классе который прописан в роутах
                if (!method_exists($controllName, $actionName)) {
                    $this->access_denied(__('Отсувствует метод: %s у класса %s', $actionName, $controllName));
                }
                # если все ок - запускаем
                $controllObject = new $controllName;
                $result = call_user_func_array([$controllObject, $actionName], $segments);
                break;
            }
        }
        if (!isset($controllObject)) {
            $this->access_denied(__('Нету подходящего роута для: %s', App::getURI()));
        }
    }
    private function access_denied(string $error)
    {
        return App::access_denied($error);
    }
}
