<?php
session_start();
define('H', $_SERVER['DOCUMENT_ROOT']);
define('TIME', time());
# доступные языки, используется в System/config/routes.ini
define('AVAILABLE_LANG', '(uk|en|ru|ko)');

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

use \Core\App;

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
