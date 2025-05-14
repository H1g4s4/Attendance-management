<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\RequestLog;
use App\Models\RequestBreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Admin */
    private $admin;

    /** @var \App\Models\RequestLog */
    private $requestLog;

    protected function setUp(): void
    {
        parent::setUp();
        // 管理者ユーザー作成・認証
        $this->admin = Admin::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('adminpass'),
        ]);
        $this->actingAs($this->admin, 'admin');

        // 一般ユーザー・勤怠・申請ログ作成
        $user = User::factory()->create();
        $att  = Attendance::factory()->create([
            'user_id' => $user->id,
            'date'    => '2025-05-10',
        ]);
        $this->requestLog = RequestLog::factory()->create([
            'user_id'         => $user->id,
            'attendance_id'   => $att->id,
            'attendance_date' => '2025-05-10',
            'start_time'      => '2025-05-10 09:30:00',
            'end_time'        => '2025-05-10 18:00:00',
            'note'            => '修正理由',
            'is_pending'      => true,
        ]);
    }

    public function test_admin_can_see_all_requests()
    {
        $response = $this->get(route('admin.requests.index', ['status' => 'pending']));
        $response->assertStatus(200)
                ->assertSee('修正理由')
                ->assertSee('承認待ち');
    }

    public function test_admin_can_approve_request_updates_attendance()
    {
        $response = $this->post(route('admin.request.approve', $this->requestLog->id));
        $response->assertRedirect(route('admin.requests.index', ['status' => 'pending']));

        // 勤怠テーブルが更新されている
        $this->assertDatabaseHas('attendances', [
            'id'         => $this->requestLog->attendance_id,
            'start_time'=> $this->requestLog->start_time,
            'end_time'  => $this->requestLog->end_time,
        ]);

        // リクエストログも承認済みに
        $this->assertDatabaseHas('request_logs', [
            'id'         => $this->requestLog->id,
            'is_pending'=> false,
        ]);
    }
}
