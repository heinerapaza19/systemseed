<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google.client_id' => 'test-id', 'services.google.client_secret' => 'test-secret']);
    }

    private function fakeGoogle(string $email = 'ana@gmail.com', string $id = 'g-123', bool $verified = true): void
    {
        $googleUser = (new SocialiteUser)->setRaw(['email_verified' => $verified])->map([
            'id' => $id, 'name' => 'Ana Torres', 'email' => $email, 'avatar' => 'https://lh3.googleusercontent.com/a/x',
        ]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_login_and_register_pages_show_google_button(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión con Google')->assertSee(route('google.redirect'), false);
        $this->get('/registro')->assertOk()->assertSee('Registrarme con Google');
    }

    public function test_redirect_goes_to_google(): void
    {
        $this->get('/auth/google')->assertRedirectContains('accounts.google.com');
    }

    public function test_redirect_without_configuration_shows_error(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/auth/google')->assertRedirect('/login')->assertSessionHasErrors('email');
    }

    public function test_callback_creates_verified_user_and_logs_in(): void
    {
        $this->fakeGoogle();

        $this->get('/auth/google/callback')->assertRedirect(route('user.dashboard'));

        $user = User::where('email', 'ana@gmail.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Ana Torres', $user->name);
        $this->assertSame('user', $user->role);
        $this->assertSame('g-123', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->password);
    }

    public function test_callback_links_existing_account_without_changing_name_or_role(): void
    {
        $admin = User::factory()->create(['email' => 'ana@gmail.com', 'name' => 'Admin Real', 'role' => 'admin']);
        $this->fakeGoogle();

        $this->get('/auth/google/callback')->assertRedirect(route('admin.dashboard'));

        $admin->refresh();
        $this->assertAuthenticatedAs($admin);
        $this->assertSame('Admin Real', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertSame('g-123', $admin->google_id);
        $this->assertSame(1, User::count());
    }

    public function test_inactive_account_cannot_enter(): void
    {
        User::factory()->create(['email' => 'ana@gmail.com', 'is_active' => false]);
        $this->fakeGoogle();

        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $this->fakeGoogle(verified: false);

        $this->get('/auth/google/callback')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(0, User::count());
    }

    public function test_cancelled_consent_returns_to_form(): void
    {
        $this->get('/auth/google/callback?error=access_denied')->assertRedirect('/login')->assertSessionHasErrors('email');
    }
}
