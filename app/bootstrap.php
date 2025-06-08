<?php
declare(strict_types=1);

use App\Core\Logger\LoggerFactory;
use App\Core\Error\Factory\ErrorHandlerFactory;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
// Ensure this file is only included once
if (defined('APP_BOOTSTRAPPED')) {
    return;
}
define('APP_BOOTSTRAPPED', true);

// Basic PHP configuration
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Load configuration
$debug = (bool) ($_ENV['APP_DEBUG'] ?? false);
$environment = $_ENV['APP_ENV'] ?? 'production';

// Create logger instance
$logger = LoggerFactory::createDefault();

// Register as global if needed
if (!function_exists('logger')) {
    function logger(): \Psr\Log\LoggerInterface {
        global $logger;
        return $logger;
    }
}

// Initialize error handling
ErrorHandlerFactory::create([
    'debug' => $debug,
    'environment' => $environment,
    'ignored_errors' => $debug ? [] : [E_DEPRECATED, E_USER_DEPRECATED]
], $logger);

return $logger;