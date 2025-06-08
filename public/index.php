<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

define('APP_DEBUG', true);
ini_set('display_errors','0');
error_reporting(E_ALL);

// Create logger
$logger = \App\Core\Logger\LoggerFactory::createDefault();

// Bootstrap error handlers with logger
\App\Core\Error\Factory\ErrorHandlerFactory::create([
    'debug' => APP_DEBUG,
    'environment' => 'web'
], $logger);

// Log application start
$logger->info('Application started');

// This will trigger an error
gfdsg();