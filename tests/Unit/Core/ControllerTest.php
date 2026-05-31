<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Controller;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    private object $controller;

    protected function setUp(): void
    {
        $this->controller = new class extends Controller {
            public function callEnsureString(mixed $value, string $default = ''): string
            {
                return $this->ensureString($value, $default);
            }

            public function callEmptyToNull(?string $value): ?string
            {
                return $this->emptyToNull($value);
            }

            public function callGetInt(string $key): ?int
            {
                return $this->getInt($key);
            }

            public function callPostInt(string $key): ?int
            {
                return $this->postInt($key);
            }
        };
    }

    public function testEnsureStringReturnsSameStringValue(): void
    {
        $this->assertSame('hello', $this->controller->callEnsureString('hello'));
    }

    public function testEnsureStringReturnsDefaultForNonString(): void
    {
        $this->assertSame('', $this->controller->callEnsureString(123));
        $this->assertSame('', $this->controller->callEnsureString(null));
        $this->assertSame('', $this->controller->callEnsureString(['arr']));
        $this->assertSame('fallback', $this->controller->callEnsureString(null, 'fallback'));
    }

    public function testEmptyToNullConvertsEmptyStringToNull(): void
    {
        $this->assertNull($this->controller->callEmptyToNull(''));
    }

    public function testEmptyToNullKeepsNonEmptyString(): void
    {
        $this->assertSame('text', $this->controller->callEmptyToNull('text'));
        $this->assertSame('   ', $this->controller->callEmptyToNull('   '));
    }

    public function testEmptyToNullPassesThroughNull(): void
    {
        $this->assertNull($this->controller->callEmptyToNull(null));
    }

    public function testGetIntReturnsNullForMissingKey(): void
    {
        $_GET = [];
        $this->assertNull($this->controller->callGetInt('id'));
    }

    public function testGetIntReturnsIntegerFromQuery(): void
    {
        $_GET = ['id' => '42'];
        $this->assertSame(42, $this->controller->callGetInt('id'));
    }

    public function testGetIntReturnsNullForInvalidValue(): void
    {
        $_GET = ['id' => 'abc'];
        $this->assertNull($this->controller->callGetInt('id'));
    }

    public function testPostIntReturnsNullForMissingKey(): void
    {
        $_POST = [];
        $this->assertNull($this->controller->callPostInt('id'));
    }

    public function testPostIntReturnsIntegerFromPost(): void
    {
        $_POST = ['id' => '15'];
        $this->assertSame(15, $this->controller->callPostInt('id'));
    }

    public function testPostIntReturnsNullForInvalidValue(): void
    {
        $_POST = ['id' => 'nope'];
        $this->assertNull($this->controller->callPostInt('id'));
    }
}
