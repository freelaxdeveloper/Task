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
];
