<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト用ユーザー作成・認証
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_current_datetime_displayed()
    {
        Carbon::setTestNow(Carbon::parse('2025-05-15 14:37:00'));
        $response = $this->get(route('user.attendance'));
        $response->assertStatus(200);
        $response->assertSee('2025年5月15日');     // 日付表示
        $response->assertSee('14:37');             // 時刻表示
    }

    public function test_status_off()
    {
        // レコードなし => 勤務外
        Attendance::where('user_id', $this->user->id)->delete();
        $response = $this->get(route('user.attendance'));
        $response->assertSee('勤務外');
    }

    public function test_status_working()
    {
        Attendance::create([
            'user_id'    => $this->user->id,
            'date'       => today(),
            'start_time' => now()->subHour(),
            'is_on_break'=> false,
        ]);
        $response = $this->get(route('user.attendance'));
        $response->assertSee('勤務中');
    }

    public function test_status_on_break()
    {
        Attendance::create([
            'user_id'     => $this->user->id,
            'date'        => today(),
            'start_time'  => now()->subHours(2),
            'is_on_break' => true,
        ]);
        $response = $this->get(route('user.attendance'));
        $response->assertSee('休憩中');
    }

    public function test_status_done()
    {
        Attendance::create([
            'user_id'    => $this->user->id,
            'date'       => today(),
            'start_time' => now()->subHours(9),
            'end_time'   => now(),
        ]);
        $response = $this->get(route('user.attendance'));
        $response->assertSee('退勤済');
    }
}
