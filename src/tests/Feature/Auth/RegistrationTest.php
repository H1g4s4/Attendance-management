<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function invalidRegistrationProvider()
    {
        return [
            // [入力データ, エラー対象フィールド, 期待メッセージ]
            [['name' => '',      'email' => 'a@b.com', 'password' => 'password', 'password_confirmation'=>'password'], 'name',     'お名前を入力してください'],
            [['name' => 'Taro',  'email' => '',        'password' => 'password', 'password_confirmation'=>'password'], 'email',    'メールアドレスを入力してください'],
            [['name' => 'Taro',  'email' => 'a@b.com', 'password' => 'short',    'password_confirmation'=>'short'],    'password', 'パスワードは8文字以上で入力してください'],
            [['name' => 'Taro',  'email' => 'a@b.com', 'password' => 'password','password_confirmation'=>'wrong'],   'password', 'パスワードと一致しません'],
            [['name' => 'Taro',  'email' => 'a@b.com', 'password' => '',         'password_confirmation'=>''],        'password', 'パスワードを入力してください'],
        ];
    }

    /**
     * @dataProvider invalidRegistrationProvider
     */
    public function test_registration_validation_errors(array $payload, string $field, string $message)
    {
        $response = $this->post(route('register'), $payload);
        $response->assertSessionHasErrors([$field => $message]);
    }

    public function test_successful_registration_persists_user()
    {
        $payload = [
            'name' => 'Taro',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $payload);
        $response->assertRedirect(route('user.attendance'));

        $this->assertDatabaseHas('users', [
            'name' => 'Taro',
            'email' => 'taro@example.com',
        ]);
    }
}
