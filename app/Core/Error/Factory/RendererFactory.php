<?php
declare(strict_types=1);

namespace App\Core\Error\Factory;

use App\Core\Error\Interfaces\ExceptionRendererInterface;
// …
final class RendererFactory{
    public function make(string $env, bool $debug): ExceptionRendererInterface
    {
        return match($env) {
            'api'     => new JsonExceptionRenderer($debug),
            'console' => new ConsoleExceptionRenderer(),
            default   => new HtmlExceptionRenderer($debug),
        };
    }
}
