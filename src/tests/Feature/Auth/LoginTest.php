<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->create([
            'email' => 'foo@bar.com',
            'password' => bcrypt('secret123'),
        ]);
    }

    public function invalidLoginProvider()
    {
        return [
            [['email' => '',           'password' => 'secret123'], 'email',    'メールアドレスを入力してください'],
            [['email' => 'foo@bar.com','password' => ''],          'password', 'パスワードを入力してください'],
            [['email' => 'wrong@bar.com','password' => 'nopass'],         null,       'ログイン情報が登録されていません'],
        ];
    }

    /**
     * @dataProvider invalidLoginProvider
     */
    public function test_login_validation_and_authenticate(array $payload, ?string $field, string $message)
    {
        $response = $this->post(route('login'), $payload);

        if ($field) {
            // フィールドレベルのバリデーション
            $response->assertSessionHasErrors([$field => $message]);
        } else {
            // 認証失敗時のメッセージ
            $response->assertSessionHasErrors(['email' => $message]);
        }
    }

    public function test_successful_login_redirects_to_attendance()
    {
        $response = $this->post(route('login'), [
            'email' => 'foo@bar.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('user.attendance'));
        $this->assertAuthenticated();
    }
}
