<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';

define('APP_DEBUG', true);

// Bootstrap application (returns logger instance)
$logger = require __DIR__.'/../app/bootstrap.php';

// Debug: Log request details to understand double requests
$logger->debug('Request received', [
    'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'time' => microtime(true)
]);

// Only log "Application started" for actual page requests
if (($_SERVER['REQUEST_URI'] ?? '') !== '/favicon.ico') {
    $logger->info('Application started');
}
$detector        = new EnvironmentDetector();
$rendererFactory = new RendererFactory();
$bootstrapper    = new ErrorBootstrapper($detector, $rendererFactory, $logger, APP_DEBUG);
$bootstrapper->register();

// This will trigger an error
gfdsg();