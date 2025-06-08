<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

define('APP_DEBUG', true);

// Bootstrap application (returns logger instance)
$logger = require __DIR__.'/../app/bootstrap.php';

// Log application start
$logger->info('Application started');

// This will trigger an error
gfdsg();