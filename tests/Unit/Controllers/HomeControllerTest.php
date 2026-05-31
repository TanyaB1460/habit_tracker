<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\HomeController;
use App\Exceptions\ValidationException;
use App\Models\Habit;
use App\Repositories\HabitRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class HomeControllerTest extends TestCase
{
    public function testToggleActionCallsRepositoryAndRedirects(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('toggleForDate')
            ->with(3, '2024-05-01');

        $controller = new HomeController($repository);
        $response = $controller->toggleAction(3, '2024-05-01');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/', $response->getHeaderLine('Location'));
    }

    public function testToggleActionThrowsValidationExceptionForInvalidId(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->never())->method('toggleForDate');

        $controller = new HomeController($repository);

        $this->expectException(ValidationException::class);
        $controller->toggleAction(0, '2024-05-01');
    }

    public function testToggleRouteExtractsDataFromRequest(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('toggleForDate')
            ->with(5, '2024-06-10');

        $request = (new ServerRequest('POST', '/toggle'))
            ->withParsedBody(['habit_id' => '5', 'date' => '2024-06-10']);

        $response = (new HomeController($repository))->toggle($request);

        $this->assertSame(303, $response->getStatusCode());
    }

    public function testToggleRouteUsesTodayWhenDateMissing(): void
    {
        $repository = $this->createMock(HabitRepository::class);
        $repository->expects($this->once())
            ->method('toggleForDate')
            ->with(2, date('Y-m-d'));

        $request = (new ServerRequest('POST', '/toggle'))
            ->withParsedBody(['habit_id' => '2']);

        (new HomeController($repository))->toggle($request);
    }
}
