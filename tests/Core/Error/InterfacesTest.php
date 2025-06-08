<?php
declare(strict_types=1);

namespace Tests\Core\Error;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use App\Core\Error\Interfaces\ErrorHandlerInterface;
use App\Core\Error\Interfaces\ExceptionRendererInterface;
use ReflectionClass;

#[CoversClass(ErrorHandlerInterface::class)]
#[CoversClass(ExceptionRendererInterface::class)]
#[CoversMethod(ErrorHandlerInterface::class, 'register')]
#[CoversMethod(ErrorHandlerInterface::class, 'handleError')]
#[CoversMethod(ExceptionRendererInterface::class, 'render')]
final class InterfacesTest extends TestCase
{
    public function testErrorHandlerInterfaceExists(): void
    {
        $rc = new ReflectionClass(ErrorHandlerInterface::class);
        $this->assertTrue($rc->hasMethod('register'));
        $this->assertTrue($rc->hasMethod('handleError'));
    }

    public function testExceptionRendererInterfaceExists(): void
    {
        $rc = new ReflectionClass(ExceptionRendererInterface::class);
        $this->assertTrue($rc->hasMethod('render'));
    }
}
