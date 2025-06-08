<?php
namespace App\Core\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    private array $handlers = [];
    private array $processors = [];
    
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }
    
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
    
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
    
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }
    
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    
    public function log($level, $message, array $context = []): void
    {
        $record = [
            'level' => $level,
            'message' => $this->interpolate($message, $context),
            'context' => $context,
            'datetime' => new \DateTimeImmutable(),
            'extra' => []
        ];
        
        // Process the record
        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }
        
        // Send to handlers
        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                $handler->handle($record);
            }
        }
    }
    
    public function addHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;
        return $this;
    }
    
    public function addProcessor(callable $processor): self
    {
        $this->processors[] = $processor;
        return $this;
    }
    
    private function interpolate($message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}