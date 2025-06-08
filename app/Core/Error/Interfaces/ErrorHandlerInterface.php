<?php
namespace App\Core\Error\Interfaces;
interface ErrorHandlerInterface
{
    public function register(): void;
    public function handleError(int $level, string $message, string $file, int $line): void;
}
