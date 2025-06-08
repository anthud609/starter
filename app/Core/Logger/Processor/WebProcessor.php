<?php
namespace App\Core\Logger\Processor;

class WebProcessor
{
    public function __invoke(array $record): array
    {
        if (PHP_SAPI !== 'cli') {
            $record['extra']['request'] = [
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'uri' => $_SERVER['REQUEST_URI'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ];
        }
        
        $record['extra']['memory_usage'] = memory_get_usage(true);
        $record['extra']['pid'] = getmypid();
        
        return $record;
    }
}