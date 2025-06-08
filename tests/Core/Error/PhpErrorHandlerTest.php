<?php
declare(strict_types=1);

namespace Tests\Core\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use App\Core\Error\Handler\PhpErrorHandler;

#[CoversClass(PhpErrorHandler::class)]
#[CoversMethod(PhpErrorHandler::class, 'register')]
#[CoversMethod(PhpErrorHandler::class, 'handleError')]
final class PhpErrorHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        // Restore any error handler installed by PhpErrorHandler
        restore_error_handler();
        parent::tearDown();
    }

    public function testErrorIsConvertedToException(): void
    {
        $handler = new PhpErrorHandler();
        $handler->register();

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('User notice for test');

        trigger_error('User notice for test', E_USER_NOTICE);
    }
}
