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
    // 1) PHP errors → exceptions
    (new PhpErrorHandler())->register();

    if (APP_DEBUG) {
        $whoops = new Run;
        $page   = new PrettyPageHandler;
        $page->setPageTitle("Application Error");
        $whoops->pushHandler($page);
        $whoops->register();
    } else {
        // your production HtmlExceptionRenderer
        (new ExceptionHandler(new HtmlExceptionRenderer()))->register();
    }

    (new ShutdownHandler(new HtmlExceptionRenderer()))->register();
}

}
