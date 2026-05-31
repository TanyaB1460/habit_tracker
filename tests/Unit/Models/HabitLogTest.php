<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\HabitLog;
use PHPUnit\Framework\TestCase;

final class HabitLogTest extends TestCase
{
    public function testFromRowCreatesHabitLogObject(): void
    {
        $row = [
            'id' => '10',
            'habit_id' => '3',
            'completed_on' => '2024-05-01',
            'created_at' => '2024-05-01 09:00:00',
        ];

        $log = HabitLog::fromRow($row);

        $this->assertSame(10, $log->id);
        $this->assertSame(3, $log->habitId);
        $this->assertSame('2024-05-01', $log->completedOn);
        $this->assertSame('2024-05-01 09:00:00', $log->createdAt);
    }

    public function testNewHabitLogHasCorrectValues(): void
    {
        $log = new HabitLog(id: null, habitId: 5, completedOn: '2024-06-15');

        $this->assertNull($log->id);
        $this->assertSame(5, $log->habitId);
        $this->assertSame('2024-06-15', $log->completedOn);
        $this->assertNull($log->createdAt);
    }
}
