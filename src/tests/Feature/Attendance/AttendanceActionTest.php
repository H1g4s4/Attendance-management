<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_clock_in_button_works()
    {
        $response = $this->post(route('attendance.startWork'));
        $response->assertRedirect(route('user.attendance'));
        $this->assertDatabaseHas('attendances', [
            'user_id'    => $this->user->id,
            'date'       => today()->format('Y-m-d'),
        ]);
    }

    public function test_cannot_clock_in_twice()
    {
        $this->post(route('attendance.startWork'));
        $response = $this->post(route('attendance.startWork'));
        $response->assertStatus(302);
        // 2回目は新規登録されない
        $this->assertEquals(1, Attendance::where('user_id', $this->user->id)->count());
    }

    public function test_clock_in_persists_to_db()
    {
        Carbon::setTestNow('2025-05-15 09:00:00');
        $this->post(route('attendance.startWork'));
        $this->assertDatabaseHas('attendances', [
            'user_id'    => $this->user->id,
            'start_time' => '2025-05-15 09:00:00',
        ]);
    }

    public function test_start_break()
    {
        // まず出勤
        $this->post(route('attendance.startWork'));
        $response = $this->post(route('attendance.startBreak'));
        $response->assertRedirect(route('user.attendance'));
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => Attendance::first()->id,
            'break_start'   => now()->format('Y-m-d H:i:00'),
            'break_end'     => null,
        ]);
    }

    public function test_multiple_breaks()
    {
        $this->post(route('attendance.startWork'));
        for ($i = 0; $i < 2; $i++) {
            $this->post(route('attendance.startBreak'));
            $this->post(route('attendance.endBreak'));
        }
        $this->assertCount(2, BreakTime::where('attendance_id', Attendance::first()->id)->get());
    }

    public function test_break_recorded_in_list()
    {
        $this->post(route('attendance.startWork'));
        $this->post(route('attendance.startBreak'));
        $this->post(route('attendance.endBreak'));
        $response = $this->get(route('attendance.list', ['month' => today()->format('Y-m')]));
        $response->assertSee(now()->format('H:i'));
    }

    public function test_clock_out()
    {
        $this->post(route('attendance.startWork'));
        $response = $this->post(route('attendance.endWork'));
        $response->assertRedirect(route('user.attendance'));
    }

    public function test_clock_out_persists_to_db()
    {
        $this->post(route('attendance.startWork'));
        Carbon::setTestNow('2025-05-15 18:00:00');
        $this->post(route('attendance.endWork'));
        $this->assertDatabaseHas('attendances', [
            'user_id'  => $this->user->id,
            'end_time' => '2025-05-15 18:00:00',
        ]);
    }
}
