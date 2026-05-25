<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\HabitController;
use App\Exceptions\ValidationException;
use App\Repositories\HabitRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class HabitControllerTest extends TestCase
{
    public function testCreateActionReturnsRedirectResponseWhenDataIsValid(): void
    {
        $validator = new class {
            public function validate(array $data): array
            {
                return [];
            }
        };

        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with('Учёба', 'Читать 30 минут', 'daily', 1);

        $controller = new HabitController($validator, $repository);

        $response = $controller->createAction('Учёба', 'Читать 30 минут', 'daily', 1);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }

    public function testCreateActionThrowsValidationExceptionWhenValidatorReturnsErrors(): void
    {
        $validator = new class {
            public function validate(array $data): array
            {
                return [
                    'name' => 'Поле name обязательно',
                ];
            }
        };

        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->never())->method('create');

        $controller = new HabitController($validator, $repository);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Ошибка валидации');

        $controller->createAction('', null, 'daily');
    }

    public function testUpdateCallsRepositoryAndRedirects(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('update')
            ->with(10, 'Спорт', 'Бег', 'daily', 2);

        $controller = new HabitController(null, $repository);

        $request = (new ServerRequest('POST', '/habits/update'))
            ->withParsedBody([
                'id' => '10',
                'name' => 'Спорт',
                'description' => 'Бег',
                'frequency' => 'daily',
                'category_id' => '2',
            ]);

        $response = $controller->update($request);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }

    public function testDeleteCallsRepositoryAndRedirects(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('delete')
            ->with(7);

        $controller = new HabitController(null, $repository);

        $request = (new ServerRequest('POST', '/habits/delete'))
            ->withParsedBody([
                'id' => '7',
            ]);

        $response = $controller->delete($request);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }
}