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
        restore_error_handler();
        parent::tearDown();
    }

    public function testErrorIsConvertedToException(): void
    {
        $handler = new PhpErrorHandler();
        $handler->register();

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('User error for test');

        // Use E_USER_ERROR instead of E_USER_NOTICE
        // because notices are now logged, not thrown
        trigger_error('User error for test', E_USER_ERROR);
    }
    
    public function testNoticeDoesNotThrowException(): void
    {
        $handler = new PhpErrorHandler();
        $handler->register();
        
        // This should NOT throw an exception
        $result = @trigger_error('User notice for test', E_USER_NOTICE);
        
        // If we get here, the test passes
        $this->assertTrue(true);
    }
}