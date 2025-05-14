<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);

    // 1) 一般ユーザーを 10 名作成
        $users = User::factory(10)->create();

        // 2) 各ユーザーに対して過去5営業日分の勤怠＋休憩を作成
        foreach ($users as $user) {
            // 過去 1～7 日のうち、平日のみ５日分取得
            $dates = collect(range(1, 7))
                ->map(fn($i) => now()->subDays($i))
                ->filter(fn($dt) => !in_array($dt->dayOfWeek, [0,6]))
                ->take(5);

            foreach ($dates as $date) {
                // 勤怠レコード作成
                $attendance = Attendance::factory()
                    ->state([
                        'user_id' => $user->id,
                        'date'    => $date->format('Y-m-d'),
                    ])
                    ->create();

                // 休憩をランダムで 1〜2 回作成
                $breakCount = rand(1, 2);
                for ($i = 0; $i < $breakCount; $i++) {
                    // 出退勤の間に収まるよう適当に設定
                    $start = $attendance->start_time
                                ->copy()
                                ->addHours(rand(2, 3));
                    $end   = (clone $start)
                                ->addMinutes(rand(30, 60));

                    BreakTime::factory()
                        ->state([
                            'attendance_id' => $attendance->id,
                            'break_start'   => $start,
                            'break_end'     => $end,
                        ])
                        ->create();
                }
            }
        }
    }
}
