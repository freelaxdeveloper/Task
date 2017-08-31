<?php
require_once 'System/inc/start.php';
// $str = 'привет';
// $data = file_get_contents('https://api.multillect.com/translate/json/1.0/561?method=translate/api/translate&from=ru&to=en&text=' . $str . '&sig=1b779fccacb995e5971cbb7e1adc371f');
// $data = json_decode($data);
// printr($data);
// exit;

use \Core\{Router,ErrorHandler};

$ErrorHandler = new ErrorHandler();
$ErrorHandler->register();

$router = new Router();
$router->run();
