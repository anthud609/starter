<?php
namespace App\Core\Logger;

interface HandlerInterface
{
    public function isHandling(array $record): bool;
    public function handle(array $record): void;
}