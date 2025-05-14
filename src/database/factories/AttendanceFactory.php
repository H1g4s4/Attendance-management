<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        // 過去 1 か月以内のランダムな勤務日
        $date      = $this->faker->dateTimeBetween('-1 month', 'now');
        $startTime = Carbon::instance($date)
                        ->setTime(rand(8, 10), rand(0, 59));
        // 勤務 7〜9 時間後に退勤
        $endTime   = (clone $startTime)
                        ->addHours(rand(7, 9))
                        ->addMinutes(rand(0, 59));

        return [
            // user_id は Seeder でセットします
            'user_id'     => null,
            'date'        => $date->format('Y-m-d'),
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'note'        => $this->faker->optional()->sentence(),
            'is_on_break' => false,
            'is_pending'  => false,
        ];
    }
}
