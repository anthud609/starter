<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

// 1) Define APP_DEBUG as you had
define('APP_DEBUG', true);

// 2) Bootstrap returns the PSR-3 logger
$logger = require __DIR__ . '/../app/bootstrap.php';

// 3) Wire up your new error-handling pipeline
use App\Core\Error\EnvironmentDetector;
use App\Core\Error\Factory\RendererFactory;
use App\Core\Error\ErrorBootstrapper;

$detector        = new EnvironmentDetector();
$rendererFactory = new RendererFactory();
$bootstrapper    = new ErrorBootstrapper(
    $detector,
    $rendererFactory,
    $logger,
    APP_DEBUG
);
$bootstrapper->register();

// 4) Your normal request logic…
$logger->debug('Request received', [
    'uri'    => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'time'   => microtime(true),
]);

if (($_SERVER['REQUEST_URI'] ?? '') !== '/favicon.ico') {
    $logger->info('Application started');
}

// …and this will now trigger your new handlers:
gfdsg();
