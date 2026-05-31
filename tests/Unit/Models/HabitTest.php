<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Habit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HabitTest extends TestCase
{
    public function testFromRowCreatesHabitObject(): void
    {
        $row = [
            'id' => '5',
            'name' => 'Читать',
            'description' => '30 минут в день',
            'frequency' => 'daily',
            'category_id' => '2',
            'is_active' => '1',
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-02 10:00:00',
            'category_name' => 'Образование',
        ];

        $habit = Habit::fromRow($row);

        $this->assertSame(5, $habit->id);
        $this->assertSame('Читать', $habit->name);
        $this->assertSame('30 минут в день', $habit->description);
        $this->assertSame('daily', $habit->frequency);
        $this->assertSame(2, $habit->categoryId);
        $this->assertTrue($habit->isActive);
        $this->assertSame('Образование', $habit->categoryName);
    }

    public function testFromRowHandlesNullableFields(): void
    {
        $row = [
            'id' => '1',
            'name' => 'Спорт',
            'description' => null,
            'frequency' => 'weekly',
            'category_id' => null,
            'is_active' => '1',
            'category_name' => null,
        ];

        $habit = Habit::fromRow($row);

        $this->assertNull($habit->description);
        $this->assertNull($habit->categoryId);
        $this->assertNull($habit->categoryName);
    }

    public function testNewHabitHasCorrectDefaults(): void
    {
        $habit = new Habit(
            id: null,
            name: 'Медитация',
            description: null,
            frequency: 'daily',
            categoryId: null,
        );

        $this->assertNull($habit->id);
        $this->assertTrue($habit->isActive);
        $this->assertNull($habit->createdAt);
        $this->assertNull($habit->updatedAt);
    }

    #[DataProvider('frequencyLabelProvider')]
    public function testFrequencyValues(string $frequency): void
    {
        $habit = new Habit(
            id: null,
            name: 'Тест',
            description: null,
            frequency: $frequency,
            categoryId: null,
        );

        $this->assertSame($frequency, $habit->frequency);
    }

    public static function frequencyLabelProvider(): array
    {
        return [
            'daily'  => ['daily'],
            'weekly' => ['weekly'],
        ];
    }

    public function testHabitPropertiesAreMutable(): void
    {
        $habit = new Habit(
            id: 1,
            name: 'Старое',
            description: 'Старое описание',
            frequency: 'daily',
            categoryId: null,
        );

        $habit->name = 'Новое';
        $habit->description = 'Новое описание';
        $habit->frequency = 'weekly';
        $habit->categoryId = 3;

        $this->assertSame('Новое', $habit->name);
        $this->assertSame('Новое описание', $habit->description);
        $this->assertSame('weekly', $habit->frequency);
        $this->assertSame(3, $habit->categoryId);
    }
}
