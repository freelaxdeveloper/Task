<?php
# то, что требуется для работы нашего скрипта

# PDO драйвер
if (!class_exists('pdo')) {
    die('Установите драйвер PDO');
}
# версия php не ниже 7.0
if (!version_compare(PHP_VERSION, 7.0, '>=')) {
    die('Требуется PHP 7.0 и выше. У Вас установлен PHP ' . phpversion());
}
