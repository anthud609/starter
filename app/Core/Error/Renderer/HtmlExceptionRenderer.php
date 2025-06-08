<?php
namespace App\Core\Error\Renderer;

use App\Core\Error\Interfaces\ExceptionRendererInterface;

class HtmlExceptionRenderer implements ExceptionRendererInterface
{
    public function render(\Throwable $e): void
    {
        http_response_code(500);
        echo "<html><body><h1>Error:</h1><pre>"
             .htmlspecialchars($e->getMessage(), ENT_QUOTES)
             ."</pre></body></html>";
    }
}
