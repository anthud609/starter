<?php
namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

final class ShutdownHandler
{
    private ExceptionRendererInterface $renderer;

    public function __construct(ExceptionRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function register(): void
    {
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err && in_array($err['type'], [
            E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR
        ], true)) {
            $exception = new \ErrorException(
                $err['message'],
                0,
                $err['type'],
                $err['file'],
                $err['line']
            );

            // Delegate to your HTML/JSON renderer instead of throwing
            $this->renderer->render($exception);
        }
    }
}
