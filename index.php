<?php
require_once 'System/inc/start.php';

use \Core\{Router,ErrorHandler};

$ErrorHandler = new ErrorHandler();
$ErrorHandler->register();

$router = new Router();
$router->run();