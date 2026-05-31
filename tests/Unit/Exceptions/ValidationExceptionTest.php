<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

final class ValidationExceptionTest extends TestCase
{
    public function testExceptionStoresErrors(): void
    {
        $errors = [
            'name' => 'Поле name обязательно',
            'frequency' => 'Некорректная частота',
        ];

        $exception = new ValidationException($errors, 'Ошибка валидации');

        $this->assertSame('Ошибка валидации', $exception->getMessage());
        $this->assertSame($errors, $exception->getErrors());
    }

    public function testExceptionCanStoreEmptyErrorsArray(): void
    {
        $exception = new ValidationException([], 'Ошибка');

        $this->assertSame([], $exception->getErrors());
        $this->assertSame('Ошибка', $exception->getMessage());
    }

    public function testExceptionIsInstanceOfRuntimeException(): void
    {
        $exception = new ValidationException(['field' => 'error']);

        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionUsesDefaultMessage(): void
    {
        $exception = new ValidationException(['name' => 'required']);

        $this->assertSame('Ошибка валидации', $exception->getMessage());
    }
}
