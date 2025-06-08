<?php
namespace App\Core\Error\Factory;

use App\Core\Error\Handler\PhpErrorHandler;
use App\Core\Error\Handler\ExceptionHandler;
use App\Core\Error\Handler\ShutdownHandler;
use App\Core\Error\Renderer\HtmlExceptionRenderer;
 use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class ErrorHandlerFactory
{
 
public static function create(): void
{
    if (defined('APP_DEBUG') && APP_DEBUG) {
        // let Whoops handle errors, exceptions, fatals
        $whoops = new \Whoops\Run;
        $handler = new \Whoops\Handler\PrettyPageHandler;
        $handler->setPageTitle('Application Error');
        $whoops->pushHandler($handler);
        $whoops->register();
        return;
    }

    // ── PRODUCTION: wire up your own handlers ──
    (new PhpErrorHandler())->register();
    $renderer = new HtmlExceptionRenderer();
    (new ExceptionHandler($renderer))->register();
    (new ShutdownHandler($renderer))->register();
}

}
