<?php
namespace App\Core\Error\Renderer;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

class JsonExceptionRenderer implements ExceptionRendererInterface
{
    private bool $debug;
    
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }
    
    public function render(\Throwable $e): void
    {
        http_response_code(500);
        header('Content-Type: application/json');
        
        $response = [
            'error' => true,
            'message' => $this->debug ? $e->getMessage() : 'Internal Server Error',
            'code' => $e->getCode() ?: 500
        ];
        
        if ($this->debug) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ];
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}