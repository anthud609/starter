<?php
declare(strict_types=1);

namespace App\Core\Error;

final class EnvironmentDetector
{
    public function detect(): string
    {
        if (PHP_SAPI === 'cli') return 'console';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (str_contains($accept, 'application/json')) return 'api';
        return 'web';
    }
}
