<?php
# то, что требуется для работы нашего скрипта

# PDO драйвер
if (!class_exists('pdo')) {
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
