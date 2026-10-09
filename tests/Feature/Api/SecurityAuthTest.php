<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_uses_generic_response_for_existing_and_unknown_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $existing = $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->json('message');
        $unknown = $this->postJson('/api/v1/auth/forgot-password', ['email' => 'unknown@example.com'])
            ->assertOk()
            ->json('message');

        $this->assertSame($existing, $unknown);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_and_existing_tokens_are_revoked(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'OldPassword1']);
        $user->createToken('old-token');
        $token = null;

        $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])->assertOk();
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewPassword2', $user->refresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_reset_password_rejects_invalid_token_and_weak_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'abcdefgh',
            'password_confirmation' => 'abcdefgh',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'Password2',
            'password_confirmation' => 'Password2',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'El token de recuperación es inválido o expiró.');
    }

    public function test_authenticated_user_must_supply_correct_and_different_current_password(): void
    {
        $user = User::factory()->create(['password' => 'CurrentPassword1']);
        Sanctum::actingAs($user);

        $this->patchJson('/api/v1/user/password', [
            'current_password' => 'WrongPassword1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ])->assertUnprocessable()->assertJsonValidationErrors('current_password');

        $this->patchJson('/api/v1/user/password', [
            'current_password' => 'CurrentPassword1',
            'password' => 'CurrentPassword1',
            'password_confirmation' => 'CurrentPassword1',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_authenticated_user_can_change_password_and_revoke_tokens(): void
    {
        $user = User::factory()->create(['password' => 'CurrentPassword1']);
        $token = $user->createToken('current-token')->plainTextToken;

        $this->patchJson('/api/v1/user/password', [
            'current_password' => 'CurrentPassword1',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ], ['Authorization' => 'Bearer '.$token])->assertOk();

        $this->assertTrue(Hash::check('NewPassword2', $user->refresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_inactive_user_cannot_change_password(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_active' => false]));

        $this->patchJson('/api/v1/user/password', [
            'current_password' => 'password',
            'password' => 'NewPassword2',
            'password_confirmation' => 'NewPassword2',
        ])->assertForbidden();
    }

    public function test_user_can_check_status_receive_notification_and_verify_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/email/verification-status')
            ->assertOk()
            ->assertJsonPath('verified', false);

        $this->postJson('/api/v1/auth/email/verification-notification')
            ->assertOk()
            ->assertJsonPath('verified', false);
        Notification::assertSentTo($user, VerifyEmail::class);

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->getJson($url)
            ->assertOk()
            ->assertJsonPath('verified', true);
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_invalid_verification_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => 'invalid-hash',
        ]);

        $this->getJson($url)->assertForbidden();
    }

    public function test_verified_user_does_not_receive_another_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/auth/email/verification-notification')
            ->assertOk()
            ->assertJsonPath('verified', true);
        Notification::assertNothingSent();
    }

    public function test_login_rate_limiter_returns_429(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'limited-login@example.com',
                'password' => 'WrongPassword1',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => 'limited-login@example.com',
            'password' => 'WrongPassword1',
        ])->assertTooManyRequests();
    }

    public function test_password_reset_rate_limiter_returns_429(): void
    {
        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->postJson('/api/v1/auth/forgot-password', [
                'email' => 'limited-reset@example.com',
            ])->assertOk();
        }

        $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'limited-reset@example.com',
        ])->assertTooManyRequests();
    }
}
