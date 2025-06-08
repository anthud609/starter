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
        
        // Always register PHP error handler first (for warnings, notices, etc)
        (new PhpErrorHandler($logger))->register();
        
        if ($debug && $environment === 'web') {
            // Use Whoops in debug mode for web
            $whoops = new \Whoops\Run;
            
            // Add logger handler BEFORE PrettyPageHandler
            if ($logger) {
                $logHandler = new \Whoops\Handler\CallbackHandler(function($exception) use ($logger) {
                    $logger->error('Uncaught exception: ' . $exception->getMessage(), [
                        'exception' => [
                            'class' => get_class($exception),
                            'message' => $exception->getMessage(),
                            'code' => $exception->getCode(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'trace' => $exception->getTraceAsString()
                        ]
                    ]);
                });
                $whoops->pushHandler($logHandler);
            }
            
            // Then add the pretty page handler
            $handler = new \Whoops\Handler\PrettyPageHandler;
            $handler->setPageTitle('Application Error');
            $whoops->pushHandler($handler);
            
            $whoops->register();
            return;
        }
        
        // Production or non-web environments
        $renderer = self::getRenderer($environment, $debug);
        
        // Register exception and shutdown handlers
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