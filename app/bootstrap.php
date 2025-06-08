<?php
declare(strict_types=1);

use App\Core\Logger\LoggerFactory;

// Ensure this file is only included once
if (defined('APP_BOOTSTRAPPED')) {
    return $GLOBALS['app_logger'] ?? null;
}
define('APP_BOOTSTRAPPED', true);

// Basic PHP configuration
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Check if APP_DEBUG is already defined, otherwise default to false
$debug = defined('APP_DEBUG') ? constant('APP_DEBUG') : false;

// For .env support (optional)
if (!$debug && file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
    $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
}

// Create (or re-use) the global logger
if (!isset($GLOBALS['app_logger'])) {
    $GLOBALS['app_logger'] = LoggerFactory::createDefault();
}
$logger = $GLOBALS['app_logger'];

if (!function_exists('logger')) {
    function logger(): \Psr\Log\LoggerInterface {
        return $GLOBALS['app_logger'];
    }
}

return $logger;