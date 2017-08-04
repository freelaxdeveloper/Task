<?php
session_start();
define('H', $_SERVER['DOCUMENT_ROOT']);
define('TIME', time());

date_default_timezone_set('Europe/Kiev');
ini_set('display_errors', 1); // включаем показ ошибок
error_reporting(E_ALL); // показываем ошибки любого уровня

# подгружаем классы
spl_autoload_register(function ($name) {
    $name = H . '/' . str_replace('\\', '/', $name) . '.php';
    if (is_file($name)) {
        require_once $name;
    }
});
# подключаем шаблонизатор Twig
require_once H . '/Libraries/twig/autoload.php';

# ф-ция для отладки
function printr($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
