<?php

namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ErrorHandlerInterface;

class PhpErrorHandler implements ErrorHandlerInterface
{
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
    }

    public function handleError(int $level, string $message, string $file, int $line): void
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
}
