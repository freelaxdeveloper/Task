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

use \Core\{Language,App};

# ф-ция для отладки
function printr($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
function __(): string
{
    $args = func_get_args();
    $args_num = count($args);
    if (!$args_num) {
        return '';
    }
    static $language;
    $string = $args[0];

    if (!$language) {
        $language = new Language(App::user()->lang);
    }
    $string = $language->translate($string);

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
