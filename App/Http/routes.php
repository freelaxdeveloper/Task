<?php
use \App\Core\Router;

Router::add('/distribution', 'distribution@index'); // API распределения
Router::add('/distribution/admin', 'distribution@admin', 'GET|POST'); // Админкa распределения
Router::add('/distribution/admin/edit/([0-9]+)', 'distribution@edit', 'GET|POST'); // Админк распределения (редактирование)

Router::add('/project/([0-9]+)/?(today|week|month)?', 'project@view'); // просмотр заданий проекта
Router::add('/task/view/complete/([0-9]+)', 'project@viewComplete'); // просмотр завершенных заданий
Router::add('/view/(today|week|month)/([0-9]+)', 'main@last'); // просмотр заданий на сегодня
Router::add('/project/new', 'project@create', 'POST'); // добавление проекта
Router::add('/project/delete/([0-9]+)/\?token\=[0-9a-z]+', 'project@delete'); // удаление проекта
Router::add('/project/edit/([0-9]+)', 'project@edit'); // редактирование проекта
Router::add('/project/edit/([0-9]+)/save', 'project@edit', 'POST'); // редактирование проекта (сохранение)

Router::add('/task/new', 'task@create', 'POST'); // добавление задания
Router::add('/task/delete/([0-9]+)/\?token\=[0-9a-z]+', 'task@delete'); // удаление задания
Router::add('/task/complete/([0-9]+)/\?token\=[0-9a-z]+', 'task@complete'); // завершение задания
Router::add('/task/edit/([0-9]+)', 'task@edit'); // редактирование задания
Router::add('/task/edit/([0-9]+)/save', 'task@edit', 'POST'); // редактирование задания (сохранение)