<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

define('APP_DEBUG', true);   // or getenv('APP_DEBUG') === 'true'
ini_set('display_errors','0');
error_reporting(E_ALL);

// bootstrap your handlers
\App\Core\Error\Factory\ErrorHandlerFactory::create();

// now trigger the error
gfdsg();
