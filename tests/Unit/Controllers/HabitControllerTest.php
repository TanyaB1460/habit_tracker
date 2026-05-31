<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\HabitController;
use App\Exceptions\ValidationException;
use App\Models\Habit;
use App\Repositories\HabitRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class HabitControllerTest extends TestCase
{
    public function testCreateActionSavesHabitAndRedirects(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(static function (Habit $habit): bool {
                return $habit->name === 'Читать'
                    && $habit->description === '30 минут'
                    && $habit->frequency === 'daily'
                    && $habit->categoryId === 1;
            }));

        $controller = new HabitController(null, $repository);
        $response = $controller->createAction('Читать', '30 минут', 'daily', 1);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }

    public function testCreateActionThrowsValidationExceptionOnErrors(): void
    {
        $validator = new class {
            public function validate(array $data): array
            {
                return ['name' => 'Поле name обязательно'];
            }
        };

        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->never())->method('save');

        $controller = new HabitController($validator, $repository);

        $this->expectException(ValidationException::class);
        $controller->createAction('', null, 'daily');
    }

    public function testUpdateFindsHabitMutatesAndSaves(): void
    {
        $existing = new Habit(id: 10, name: 'Старое', description: null, frequency: 'daily', categoryId: null);

        $repository = $this->createMock(HabitRepository::class);
        $repository->method('findById')->with(10)->willReturn($existing);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(static function (Habit $habit): bool {
                return $habit->name === 'Спорт'
                    && $habit->description === 'Бег'
                    && $habit->frequency === 'weekly';
            }));

        $request = (new ServerRequest('POST', '/habits/update'))
            ->withParsedBody([
                'id' => '10',
                'name' => 'Спорт',
                'description' => 'Бег',
                'frequency' => 'weekly',
                'category_id' => '',
            ]);

        $response = (new HabitController(null, $repository))->update($request);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }

    public function testDeleteFindsHabitAndDeletes(): void
    {
        $habit = new Habit(id: 7, name: 'Уборка', description: null, frequency: 'weekly', categoryId: null);

        $repository = $this->createMock(HabitRepository::class);
        $repository->method('findById')->with(7)->willReturn($habit);
        $repository->expects($this->once())->method('delete')->with($habit);

        $request = (new ServerRequest('POST', '/habits/delete'))
            ->withParsedBody(['id' => '7']);

        $response = (new HabitController(null, $repository))->delete($request);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/habits', $response->getHeaderLine('Location'));
    }

    public function testDeleteDoesNothingIfHabitNotFound(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('delete');

        $request = (new ServerRequest('POST', '/habits/delete'))
            ->withParsedBody(['id' => '999']);

        $response = (new HabitController(null, $repository))->delete($request);

        $this->assertSame(303, $response->getStatusCode());
    }
}
