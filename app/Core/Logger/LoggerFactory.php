<?php
namespace App\Core\Logger;

use App\Core\Logger\Handler\FileHandler;
use App\Core\Logger\Handler\ConsoleHandler;
use App\Core\Logger\Processor\WebProcessor;
use Psr\Log\LogLevel;

class LoggerFactory
{
    public static function create(array $config = []): Logger
    {
        $logger = new Logger();
        
        // Add handlers based on config
        if ($config['handlers']['file']['enabled'] ?? true) {
            $path = $config['handlers']['file']['path'] 
                ?? dirname(__DIR__, 3) . '/storage/logs/app.log';
            
            $logger->addHandler(new FileHandler(
                $path,
                $config['handlers']['file']['level'] ?? LogLevel::DEBUG
            ));
        }
        
        // Only add console handler if explicitly enabled or in CLI mode
        if ($config['handlers']['console']['enabled'] ?? false) {
            $logger->addHandler(new ConsoleHandler(
                $config['handlers']['console']['level'] ?? LogLevel::INFO
            ));
        }
        
        // Add processors
        if ($config['processors']['web'] ?? (PHP_SAPI !== 'cli')) {
            $logger->addProcessor(new WebProcessor());
        }
        
        return $logger;
    }
    
    public static function createDefault(): Logger
    {
        $isCli = PHP_SAPI === 'cli';
        
        return self::create([
            'handlers' => [
                'file' => [
                    'enabled' => true,
                    'path' => dirname(__DIR__, 3) . '/storage/logs/app.log',
                    'level' => LogLevel::DEBUG
                ],
                'console' => [
                    'enabled' => $isCli, // Only enable console in CLI mode
                    'level' => LogLevel::INFO
                ]
            ],
            'processors' => [
                'web' => !$isCli // Only add web processor in non-CLI mode
            ]
        ]);
    }
}