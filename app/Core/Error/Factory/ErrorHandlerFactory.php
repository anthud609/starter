<?php
namespace App\Core\Error\Factory;

use App\Core\Error\Handler\PhpErrorHandler;
use App\Core\Error\Handler\ExceptionHandler;
use App\Core\Error\Handler\ShutdownHandler;
use App\Core\Error\Renderer\HtmlExceptionRenderer;
use App\Core\Error\Renderer\JsonExceptionRenderer;
use App\Core\Error\Renderer\ConsoleExceptionRenderer;
use App\Core\Error\Interfaces\ExceptionRendererInterface;
use Psr\Log\LoggerInterface;

class ErrorHandlerFactory
{
    public static function create(array $config = [], ?LoggerInterface $logger = null): void
    {
        $debug = $config['debug'] ?? (defined('APP_DEBUG') ? APP_DEBUG : false);
        $environment = $config['environment'] ?? self::detectEnvironment();
        
        if ($debug && $environment === 'web') {
            // Use Whoops in debug mode for web
            $whoops = new \Whoops\Run;
            $handler = new \Whoops\Handler\PrettyPageHandler;
            $handler->setPageTitle('Application Error');
            
            // Add logger to Whoops if available
            if ($logger) {
                $whoops->pushHandler(function($exception) use ($logger) {
                    $logger->error($exception->getMessage(), [
                        'exception' => $exception,
                        'trace' => $exception->getTraceAsString()
                    ]);
                });
            }
            
            $whoops->pushHandler($handler);
            $whoops->register();
            return;
        }
        
        // Production or non-web environments
        $renderer = self::getRenderer($environment, $debug);
        
        // Register handlers with optional logger
        (new PhpErrorHandler($logger))->register();
        (new ExceptionHandler($renderer, $logger))->register();
        (new ShutdownHandler($renderer, $logger))->register();
    }
    
    private static function detectEnvironment(): string
    {
        if (PHP_SAPI === 'cli') {
            return 'console';
        }
        
        // Check if this is likely an API request
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (str_contains($accept, 'application/json')) {
            return 'api';
        }
        
        return 'web';
    }
    
    private static function getRenderer(string $environment, bool $debug): ExceptionRendererInterface
    {
        return match($environment) {
            'api' => new JsonExceptionRenderer($debug),
            'console' => new ConsoleExceptionRenderer(),
            default => new HtmlExceptionRenderer($debug)
        };
    }
}