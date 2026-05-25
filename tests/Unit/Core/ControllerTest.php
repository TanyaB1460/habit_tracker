<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Controller;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testEnsureStringReturnsSameStringValue(): void
    {
        $controller = new TestableController();

        $this->assertSame('hello', $controller->callEnsureString('hello'));
    }

    public function testEnsureStringReturnsEmptyStringForNonStringValue(): void
    {
        $controller = new TestableController();

        $this->assertSame('', $controller->callEnsureString(123));
        $this->assertSame('', $controller->callEnsureString(null));
        $this->assertSame('', $controller->callEnsureString(['test']));
    }

    public function testEmptyToNullConvertsOnlyEmptyStringToNull(): void
    {
        $controller = new TestableController();

        $this->assertNull($controller->callEmptyToNull(''));
        $this->assertSame('   ', $controller->callEmptyToNull('   '));
        $this->assertSame('text', $controller->callEmptyToNull('text'));
    }

    public function testGetIntReturnsNullForMissingQueryValue(): void
    {
        $_GET = [];

        $controller = new TestableController();

        $this->assertNull($controller->callGetInt('id'));
    }

    public function testGetIntReturnsIntegerFromQuery(): void
    {
        $_GET = ['id' => '42'];

        $controller = new TestableController();

        $this->assertSame(42, $controller->callGetInt('id'));
    }

    public function testPostIntReturnsNullForMissingPostValue(): void
    {
        $_POST = [];

        $controller = new TestableController();

        $this->assertNull($controller->callPostInt('id'));
    }

    public function testPostIntReturnsIntegerFromPost(): void
    {
        $_POST = ['id' => '15'];

        $controller = new TestableController();

        $this->assertSame(15, $controller->callPostInt('id'));
    }
}

final class TestableController extends Controller
{
    public function callEnsureString(mixed $value): string
    {
        return $this->ensureString($value);
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
}