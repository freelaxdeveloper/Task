<?php
require_once 'System/inc/start.php';

use \App\Core\{Router,ErrorHandler,DB};

$errorHandler = new ErrorHandler;
$errorHandler->register();

require_once 'App/Http/routes.php';
Router::dispatch();

//DB::table('test')->delete(); // users