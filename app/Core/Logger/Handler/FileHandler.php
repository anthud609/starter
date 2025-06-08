<?php
namespace App\Core\Logger\Handler;

use App\Core\Logger\HandlerInterface;
use Psr\Log\LogLevel;

class FileHandler implements HandlerInterface
{
    private string $filepath;
    private string $level;
    private array $levelHierarchy = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7,
    ];
    
    public function __construct(string $filepath, string $level = LogLevel::DEBUG)
    {
        $this->filepath = $filepath;
        $this->level = $level;
    }
    
    public function isHandling(array $record): bool
    {
        return $this->levelHierarchy[$record['level']] >= $this->levelHierarchy[$this->level];
    }
    
    public function handle(array $record): void
    {
        $dir = dirname($this->filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $formatted = sprintf(
            "[%s] %s: %s %s\n",
            $record['datetime']->format('Y-m-d H:i:s'),
            strtoupper($record['level']),
            $record['message'],
            !empty($record['context']) ? json_encode($record['context']) : ''
        );
        
        file_put_contents($this->filepath, $formatted, FILE_APPEND | LOCK_EX);
    }
}