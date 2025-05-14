<?php

namespace Database\Factories;

use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        // Seeder で attendance_id, break_start, break_end を明示的に渡すので
        // ここではダミー値を用意しておく程度です。
        $start = Carbon::now()->subHours(rand(1, 2));
        $end   = (clone $start)->addMinutes(rand(30, 60));

        return [
            'attendance_id' => null,
            'break_start'   => $start,
            'break_end'     => $end,
        ];
    }
}

