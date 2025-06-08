<?php
namespace App\Core\Error\Renderer;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

class HtmlExceptionRenderer implements ExceptionRendererInterface
{
    private bool $debug;
    
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }
    
    public function render(\Throwable $e): void
    {
        http_response_code(500);
        
        if (!$this->debug) {
            $this->renderProductionError();
            return;
        }
        
        $this->renderDebugError($e);
    }
    
    private function renderProductionError(): void
    {
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Server Error</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
            background: #f5f5f5; 
        }
        .error-container { 
            text-align: center; 
            padding: 40px; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        h1 { color: #333; margin: 0 0 10px 0; }
        p { color: #666; margin: 0; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Something went wrong</h1>
        <p>We're experiencing technical difficulties. Please try again later.</p>
    </div>
</body>
</html>
HTML;
    }
    
    private function renderDebugError(\Throwable $e): void
    {
        $class = get_class($e);
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES);
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES);
        $line = $e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES);
        
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Error: {$message}</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        h1 { color: #e74c3c; margin-top: 0; }
        .exception-type { color: #7f8c8d; font-size: 0.9em; }
        .file-info { 
            background: #ecf0f1; 
            padding: 15px; 
            border-radius: 4px; 
            margin: 20px 0; 
            font-family: 'Consolas', 'Monaco', monospace; 
        }
        .trace { 
            background: #2c3e50; 
            color: #ecf0f1; 
            padding: 20px; 
            border-radius: 4px; 
            overflow-x: auto; 
        }
        .trace pre { margin: 0; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{$message}</h1>
        <p class="exception-type">{$class}</p>
        
        <div class="file-info">
            <strong>File:</strong> {$file}<br>
            <strong>Line:</strong> {$line}
        </div>
        
        <h3>Stack Trace:</h3>
        <div class="trace">
            <pre>{$trace}</pre>
        </div>
    </div>
</body>
</html>
HTML;
    }
}