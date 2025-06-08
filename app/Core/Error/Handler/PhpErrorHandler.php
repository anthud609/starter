<?php
namespace App\Core\Error\Handler;

use App\Core\Error\Interfaces\ErrorHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PhpErrorHandler implements ErrorHandlerInterface
{
    private ?LoggerInterface $logger;
    
    private array $errorLevelMap = [
        E_ERROR => LogLevel::ERROR,
        E_WARNING => LogLevel::WARNING,
        E_PARSE => LogLevel::CRITICAL,
        E_NOTICE => LogLevel::NOTICE,
        E_CORE_ERROR => LogLevel::CRITICAL,
        E_CORE_WARNING => LogLevel::WARNING,
        E_COMPILE_ERROR => LogLevel::CRITICAL,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::ERROR,
        E_USER_WARNING => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::NOTICE,
        E_STRICT => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED => LogLevel::NOTICE,
        E_USER_DEPRECATED => LogLevel::NOTICE,
    ];
    
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
    }

    public function handleError(int $level, string $message, string $file, int $line): bool
    {
        // Don't handle errors suppressed with @
        if (!(error_reporting() & $level)) {
            return false;
        }
        
        // Log the error if logger is available
        if ($this->logger) {
            $logLevel = $this->errorLevelMap[$level] ?? LogLevel::ERROR;
            $this->logger->log($logLevel, "PHP Error: {$message}", [
                'error_level' => $level,
                'file' => $file,
                'line' => $line
            ]);
        }
        
        // Convert errors to exceptions (except warnings/notices)
        if (!in_array($level, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED])) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
        
        return true; // Continue execution for warnings/notices
    }
}