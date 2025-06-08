<?php
namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ExceptionRendererInterface;
use Psr\Log\LoggerInterface;

final class ShutdownHandler
{
    private ExceptionRendererInterface $renderer;
    private ?LoggerInterface $logger;

    public function __construct(
        ExceptionRendererInterface $renderer,
        ?LoggerInterface $logger = null
    ) {
        $this->renderer = $renderer;
        $this->logger = $logger;
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

            // Log fatal error if logger is available
            if ($this->logger) {
                $this->logger->critical('Fatal error during shutdown', [
                    'message' => $err['message'],
                    'file' => $err['file'],
                    'line' => $err['line'],
                    'type' => $err['type']
                ]);
            }

            // Delegate to renderer
            $this->renderer->render($exception);
        }
    }
}