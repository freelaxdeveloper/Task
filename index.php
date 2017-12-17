<?php
session_start();
define('H', $_SERVER['DOCUMENT_ROOT']);
define('TIME', time());
# доступные языки, используется в роутах
define('AVAILABLE_LANG', '(uk|en|ru|ko)');

date_default_timezone_set('Europe/Kiev');
ini_set('display_errors', 1); // включаем показ ошибок
error_reporting(E_ALL); // показываем ошибки любого уровня

# PDO драйвер
if (!class_exists('pdo') || array_search('mysql', \PDO::getAvailableDrivers()) === false) {
    die('Установите PDO драйвер');
}
# версия php не ниже 7.0
if (!version_compare(PHP_VERSION, 7.0, '>=')) {
    die('Требуется версия PHP не ниже 7.0. Ваша версия PHP ' . phpversion());
}
# библиотека GD
if (!in_array('gd', get_loaded_extensions())) {
    die('Требуется поддержка GD');
}

use \App\Core\App;

# ф-ция для отладки
function debug($array)
{
    echo '<pre>' . print_r($array, true) . '</pre>';
}
# мультиязычность
function __(): string
{
    $args = func_get_args();
    $args_num = count($args);
    if (!$args_num) {
        return '';
    }
    $string = App::language()->translate($args[0]);

    if ($args_num == 1) {
        return $string; // строка без параметров
    }
    // строка с параметрами
    $args4eval = array();
    for ($i = 1; $i < $args_num; $i++) {
        $args4eval[] = '$args[' . $i . ']';
    }
    return eval('return sprintf($string,' . implode(',', $args4eval) . ');');
}

require_once 'vendor/autoload.php';

use \App\Core\{Router,ErrorHandler,DB};

// $errorHandler = new ErrorHandler;
// $errorHandler->register();

require_once 'App/Http/routes.php';
Router::dispatch();

//DB::table('test')->delete(); // users