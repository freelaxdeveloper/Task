<?php
require_once 'System/inc/start.php';

use \Core\{Router,ErrorHandler};

$errorHandler = new ErrorHandler;
$errorHandler->register();

Router::add('test', 'main/index');
Router::dispatch();