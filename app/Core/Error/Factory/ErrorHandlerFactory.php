<?php
namespace App\Core\Error\Factory;

use App\Core\Error\Handler\PhpErrorHandler;
use App\Core\Error\Handler\ExceptionHandler;
use App\Core\Error\Handler\ShutdownHandler;
use App\Core\Error\Renderer\HtmlExceptionRenderer;

class ErrorHandlerFactory
{
    public static function create(): void
    {
        $phpHandler = new PhpErrorHandler();
        $phpHandler->register();

        $exceptionRenderer = new HtmlExceptionRenderer();
        $exceptionHandler  = new ExceptionHandler($exceptionRenderer);
        $exceptionHandler->register();

        (new ShutdownHandler())->register();
    }
}
