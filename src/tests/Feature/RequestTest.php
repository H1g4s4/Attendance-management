<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\RequestLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Attendance */
    private $attendance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user       = User::factory()->create();
        $this->attendance = Attendance::factory()->create([
            'user_id'    => $this->user->id,
            'date'       => '2025-05-10',
            'start_time' => '2025-05-10 09:00:00',
            'end_time'   => '2025-05-10 18:00:00',
        ]);
        $this->actingAs($this->user);
    }

    public function test_validation_on_request_submission()
    {
        // 出勤＞退勤エラー
        $payload = [
            'attendance_date' => '2025-05-10',
            'start_time'      => '18:00',
            'end_time'        => '09:00',
            'note'            => '理由',
            'breaks'          => [],
        ];
        $response = $this->post(route('user.requests.store'), $payload);
        $response->assertSessionHasErrors(['end_time']);

        // 備考未入力エラー
        $payload = [
            'attendance_date' => '2025-05-10',
            'start_time'      => '09:00',
            'end_time'        => '18:00',
            'note'            => '',
            'breaks'          => [],
        ];
        $response = $this->post(route('user.requests.store'), $payload);
        $response->assertSessionHasErrors(['note']);
    }

    public function test_request_list_shows_pending_and_approved()
    {
        // pending
        RequestLog::factory()->create([
            'user_id'         => $this->user->id,
            'attendance_date' => '2025-05-10',
            'is_pending'      => true,
        ]);
        // approved
        RequestLog::factory()->create([
            'user_id'         => $this->user->id,
            'attendance_date' => '2025-05-09',
            'is_pending'      => false,
        ]);

        $pending = $this->get(route('user.requests', ['status' => 'pending']));
        $pending->assertSee('2025/05/10')->assertDontSee('2025/05/09');

        $approved = $this->get(route('user.requests', ['status' => 'approved']));
        $approved->assertSee('2025/05/09')->assertDontSee('2025/05/10');
    }

    public function test_request_detail_page()
    {
        $log = RequestLog::factory()->create([
            'user_id'         => $this->user->id,
            'attendance_date' => '2025-05-10',
            'start_time'      => '2025-05-10 10:00:00',
            'end_time'        => '2025-05-10 17:00:00',
            'note'            => '理由',
        ]);

        $response = $this->get(route('user.requests.detail', $log->id));
        $response->assertStatus(200)
                ->assertSee('10:00')
                ->assertSee('17:00')
                ->assertSee('理由');
    }
}
