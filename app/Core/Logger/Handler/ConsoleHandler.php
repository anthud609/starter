<?php
namespace App\Core\Logger\Handler;

use App\Core\Logger\HandlerInterface;
use Psr\Log\LogLevel;

class ConsoleHandler implements HandlerInterface
{
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
    
    private array $colors = [
        LogLevel::DEBUG => "\033[0;36m",     // Cyan
        LogLevel::INFO => "\033[0;32m",      // Green
        LogLevel::NOTICE => "\033[0;34m",    // Blue
        LogLevel::WARNING => "\033[0;33m",   // Yellow
        LogLevel::ERROR => "\033[0;31m",     // Red
        LogLevel::CRITICAL => "\033[1;31m",  // Bold Red
        LogLevel::ALERT => "\033[1;35m",     // Bold Magenta
        LogLevel::EMERGENCY => "\033[1;37;41m", // White on Red
    ];
    
    public function __construct(string $level = LogLevel::DEBUG)
    {
        $this->level = $level;
    }
    
    public function isHandling(array $record): bool
    {
        return $this->levelHierarchy[$record['level']] >= $this->levelHierarchy[$this->level];
    }
    
    public function handle(array $record): void
    {
        $color = $this->colors[$record['level']] ?? '';
        $reset = "\033[0m";
        
        $output = sprintf(
            "%s[%s] %s:%s %s %s\n",
            $color,
            $record['datetime']->format('Y-m-d H:i:s'),
            strtoupper($record['level']),
            $reset,
            $record['message'],
            !empty($record['context']) ? json_encode($record['context']) : ''
        );
        
        $stream = in_array($record['level'], [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY]) 
            ? STDERR 
            : STDOUT;
            
        fwrite($stream, $output);
    }
}