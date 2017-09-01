<?php
use \Core\App;

/*
 @param pattern - адрес по которому доступна страница
 @param run - вызываемый класс/метод/параметр1/параметр2...
 @param method - способ передачи данных POST/GET
*/
return [
    # каптча
    ['pattern' => 'captcha\.jpg','run' => 'captcha/view','method' => 'GET'],
    # главная страница
    ['pattern' => '[a-z]{2}','run' => 'main/index','method' => 'GET'],
    # F.A.Q
    ['pattern' => '[a-z]{2}/faq','run' => 'main/faq','method' => 'GET'],
    # страница регистрации
    ['pattern' => '[a-z]{2}/register','run' => 'authorize/register','method' => 'GET'],
    # страница регистрации (отправка формы)
    ['pattern' => '[a-z]{2}/register/send','run' => 'authorize/register','method' => 'POST'],
    # страница авторизации
    ['pattern' => '[a-z]{2}/authorize','run' => 'authorize/authorize','method' => 'GET'],
    # страница авторизации (отправка формы)
    ['pattern' => '[a-z]{2}/authorize/send','run' => 'authorize/authorize','method' => 'POST'],
    # выход с профиля
    ['pattern' => '[a-z]{2}/exit/\?token\=[0-9a-z]+','run' => 'authorize/exit','method' => 'GET'],
    # удаление пользователя
    ['pattern' => '[a-z]{2}/user/delete/([0-9]+)','run' => 'user/delete/$1','method' => 'GET|POST'],
    # просмотр заданий проекта
    ['pattern' => '[a-z]{2}/project/([0-9]+)/?(today|week|month)?','run' => 'project/view/$1/$2','method' => 'GET'],
    # просмотр завершенных заданий
    ['pattern' => '[a-z]{2}/task/view/complete/([0-9]+)','run' => 'project/viewComplete/$1','method' => 'GET'],
    # просмотр заданий на сегодня
    ['pattern' => '[a-z]{2}/view/(today|week|month)/([0-9]+)','run' => 'main/last/$1/$2','method' => 'GET'],
    # добавление проекта
    ['pattern' => '[a-z]{2}/project/new','run' => 'project/create','method' => 'POST'],
    # удаление проекта
    ['pattern' => '[a-z]{2}/project/delete/([0-9]+)/\?token\=[0-9a-z]+','run' => 'project/delete/$1','method' => 'GET'],
    # редактирование проекта
    ['pattern' => '[a-z]{2}/project/edit/([0-9]+)','run' => 'project/edit/$1','method' => 'GET'],
    # редактирование проекта (сохранение)
    ['pattern' => '[a-z]{2}/project/edit/([0-9]+)/save','run' => 'project/edit/$1','method' => 'POST'],
    # добавление задания
    ['pattern' => '[a-z]{2}/task/new','run' => 'task/create','method' => 'POST'],
    # удаление задания
    ['pattern' => '[a-z]{2}/task/delete/([0-9]+)/\?token\=[0-9a-z]+','run' => 'task/delete/$1','method' => 'GET'],
    # завершение задания
    ['pattern' => '[a-z]{2}/task/complete/([0-9]+)/\?token\=[0-9a-z]+','run' => 'task/complete/$1','method' => 'GET'],
    # редактирование задания
    ['pattern' => '[a-z]{2}/task/edit/([0-9]+)','run' => 'task/edit/$1','method' => 'GET'],
    # редактирование задания (сохранение)
    ['pattern' => '[a-z]{2}/task/edit/([0-9]+)/save','run' => 'task/edit/$1','method' => 'POST'],
];
