<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('adminpass'),
        ]);
    }

    public function invalidAdminLoginProvider()
    {
        return [
            [['email'=>'','password'=>'adminpass'],           'email',    'メールアドレスを入力してください'],
            [['email'=>'admin@example.com','password'=>''],   'password', 'パスワードを入力してください'],
            [['email'=>'wrong@admin.com','password'=>'bad'],  null,       'ログイン情報が登録されていません'],
        ];
    }

    /**
     * @dataProvider invalidAdminLoginProvider
     */
    public function test_admin_login_errors(array $payload, ?string $field, string $message)
    {
        $response = $this->post(route('admin.login.submit'), $payload);

        if ($field) {
            $response->assertSessionHasErrors([$field => $message]);
        } else {
            $response->assertSessionHasErrors(['email' => $message]);
        }
    }

    public function test_admin_successful_login()
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'adminpass',
        ]);

        $response->assertRedirect(route('admin.attendance.index'));
        $this->assertAuthenticated('admin');
    }
}
