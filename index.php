<?php
require_once 'System/inc/start.php';

use \Core\{Router,ErrorHandler};

$errorHandler = new ErrorHandler;
$errorHandler->register();

require_once 'App/Http/routes.php';
Router::dispatch();