<?php
namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ExceptionRendererInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class ExceptionHandler
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
        set_exception_handler([$this, 'handleError']);
    }

    public function handleError(\Throwable $e): void
    {
        // Log the exception if logger is available
        if ($this->logger) {
            $this->logger->error('Uncaught exception: ' . $e->getMessage(), [
                'exception' => [
                    'class' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
        
        // Render the response
        $this->renderer->render($e);
    }
}