<?php
use \Core\App;

/*
 @param pattern - адрес по которому доступна страница
 @param run - вызываемый класс/метод/параметр1/параметр2...
 @param method - способ передачи данных POST/GET
*/
return [
    # главная страница
    ['pattern' => '','run' => 'main/index','method' => 'GET'],
    # страница регистрации
    ['pattern' => 'register','run' => 'authorize/register','method' => 'GET'],
    # страница регистрации (отправка формы)
    ['pattern' => 'register/send','run' => 'authorize/register','method' => 'POST'],
    # страница авторизации
    ['pattern' => 'authorize','run' => 'authorize/authorize','method' => 'GET'],
    # страница авторизации (отправка формы)
    ['pattern' => 'authorize/send','run' => 'authorize/authorize','method' => 'POST'],
    # выход с профиля
    ['pattern' => 'exit','run' => 'authorize/exit','method' => 'GET'],
    # просмотр заданий проекта
    ['pattern' => 'project/([0-9]+)','run' => 'project/view/$1','method' => 'GET'],
    # удаление проекта
    ['pattern' => 'project/delete/([0-9]+)','run' => 'project/delete/$1','method' => 'GET'],
    # удаление задания
    ['pattern' => 'task/delete/([0-9]+)','run' => 'task/delete/$1','method' => 'GET'],
];
