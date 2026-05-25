<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Controller;
use App\Exceptions\ValidationException;
use App\Models\HabitCategory;
use App\Repositories\HabitRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HabitController extends Controller
{
    private ?object $habitValidator;
    private HabitRepository $habitRepository;

    public function __construct(
        ?object $habitValidator = null,
        ?HabitRepository $habitRepository = null
    ) {
        $this->habitValidator = $habitValidator;
        $this->habitRepository = $habitRepository ?? new HabitRepository();
    }

    #[Route(path: '/habits', methods: ['GET'])]
    public function index(): ResponseInterface
    {
        $habits = $this->habitRepository->getAll();
        $categories = HabitCategory::getAll();

        return $this->render('habits/index', [
            'habits' => $habits,
            'categories' => $categories,
        ]);
    }

    public function createAction(
        string $name,
        ?string $description,
        string $frequency,
        ?int $categoryId = null
    ): ResponseInterface {
        $errors = [];

        if ($this->habitValidator !== null) {
            $errors = $this->habitValidator->validate([
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
                'category_id' => $categoryId,
            ]);
        }

        if ($errors !== []) {
            throw new ValidationException($errors, 'Ошибка валидации');
        }

        $this->habitRepository->create($name, $description, $frequency, $categoryId);

        return $this->redirect('/habits');
    }

    #[Route(path: '/habits/create', methods: ['POST'])]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $name = is_string($data['name'] ?? null) ? trim($data['name']) : '';
        $description = is_string($data['description'] ?? null) ? trim($data['description']) : '';
        $frequency = is_string($data['frequency'] ?? null) ? trim($data['frequency']) : 'daily';
        $categoryId = isset($data['category_id']) && $data['category_id'] !== ''
            ? (int) $data['category_id']
            : null;

        return $this->createAction(
            $name,
            $description !== '' ? $description : null,
            $frequency,
            $categoryId
        );
    }

    #[Route(path: '/habits/edit', methods: ['GET'])]
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $id = isset($query['id']) ? (int) $query['id'] : 0;

        if ($id <= 0) {
            return $this->render('errors/404', [], 404);
        }

        $habit = $this->habitRepository->findById($id);

        if ($habit === null) {
            return $this->render('errors/404', [], 404);
        }

        $categories = HabitCategory::getAll();

        return $this->render('habits/edit', [
            'habit' => $habit,
            'categories' => $categories,
        ]);
    }

    #[Route(path: '/habits/update', methods: ['POST'])]
    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $id = isset($data['id']) ? (int) $data['id'] : 0;
        $name = is_string($data['name'] ?? null) ? trim($data['name']) : '';
        $description = is_string($data['description'] ?? null) ? trim($data['description']) : '';
        $frequency = is_string($data['frequency'] ?? null) ? trim($data['frequency']) : 'daily';
        $categoryId = isset($data['category_id']) && $data['category_id'] !== ''
            ? (int) $data['category_id']
            : null;

        if ($id > 0 && $name !== '') {
            $this->habitRepository->update(
                $id,
                $name,
                $description !== '' ? $description : null,
                $frequency,
                $categoryId
            );
        }

        return $this->redirect('/habits');
    }

    #[Route(path: '/habits/delete', methods: ['POST'])]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $id = isset($data['id']) ? (int) $data['id'] : 0;

        if ($id > 0) {
            $this->habitRepository->delete($id);
        }

        return $this->redirect('/habits');
    }
}