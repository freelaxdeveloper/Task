<?php
# то, что требуется для работы нашего скрипта

# PDO драйвер
if (!class_exists('pdo')) {
    die('Install the PDO driver');
}
# версия php не ниже 7.0
if (!version_compare(PHP_VERSION, 7.0, '>=')) {
    die('Requires PHP 7.0 or higher. You have PHP installed ' . phpversion());
}
