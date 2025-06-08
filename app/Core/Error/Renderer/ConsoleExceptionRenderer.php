<?php
namespace App\Core\Error\Renderer;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

class ConsoleExceptionRenderer implements ExceptionRendererInterface
{
    public function render(\Throwable $e): void
    {
        $output = PHP_EOL . "\033[1;31m[ERROR]\033[0m " . get_class($e) . PHP_EOL;
        $output .= "\033[1;33mMessage:\033[0m " . $e->getMessage() . PHP_EOL;
        $output .= "\033[1;33mFile:\033[0m " . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
        $output .= "\033[1;33mTrace:\033[0m" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
        
        fwrite(STDERR, $output);
        exit(1);
    }
}