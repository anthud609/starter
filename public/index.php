<?php
declare(strict_types=1);

// 1) Autoload all App\ classes
require __DIR__ . '/../vendor/autoload.php';

// 2) Disable default PHP error display
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// 3) Bootstrap your Core error subsystem
\App\Core\Error\Factory\ErrorHandlerFactory::create();

// 4) Now your application code—any error here will be caught
gfdsg();
