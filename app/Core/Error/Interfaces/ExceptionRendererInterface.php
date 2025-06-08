<?php

namespace App\Core\Error\Interfaces;
interface ExceptionRendererInterface
{
    public function render(\Throwable $e): void;
}
