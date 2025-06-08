<?php
declare(strict_types=1);

namespace Tests\Core\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use App\Core\Error\Handler\ExceptionHandler;
use App\Core\Error\Interfaces\ExceptionRendererInterface;

#[CoversClass(ExceptionHandler::class)]
#[CoversMethod(ExceptionHandler::class, 'register')]
#[CoversMethod(ExceptionHandler::class, 'handleError')]
final class ExceptionHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        // Restore the global exception handler installed in register()
        restore_exception_handler();
        parent::tearDown();
    }

    public function testExceptionIsDelegatedToRenderer(): void
    {
        // A dummy renderer that captures output
        $renderer = new class implements ExceptionRendererInterface {
            public string $out = '';
            public function render(\Throwable $e): void
            {
                $this->out = get_class($e) . ':' . $e->getMessage();
            }
        };

        $handler = new ExceptionHandler($renderer);
        $handler->register();

        // Directly invoke the handler
        $exception = new \RuntimeException('boom');
        $handler->handleError($exception);

        $this->assertStringContainsString('RuntimeException:boom', $renderer->out);
    }
}
