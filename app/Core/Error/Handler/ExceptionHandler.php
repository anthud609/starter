<?php
declare(strict_types=1);

namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

final class ExceptionHandler
{
    private ExceptionRendererInterface $renderer;

    public function __construct(ExceptionRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function register(): void
    {
        set_exception_handler([$this, 'handleError']);
    }

    public function handleError(\Throwable $e): void
    {
        $this->renderer->render($e);
    }
}
