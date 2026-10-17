<?php

namespace Tests\Feature\Web;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_spanish_and_points_to_web(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['name' => 'Ana Torres']);
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            $mail = $notification->toMail($user);

            return $mail->subject === 'Confirma tu correo · SystemSeed'
                && $mail->greeting === '¡Hola, Ana!'
                && str_contains($mail->actionUrl, '/verificar-correo/');
        });
    }

    public function test_verification_link_works_without_session(): void
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.web', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->get($url)->assertRedirect(route('login'))->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertGuest();
    }

    public function test_verification_link_while_logged_in_goes_to_dashboard(): void
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.web', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->actingAs($user)->get($url)->assertRedirect(route('user.dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_tampered_or_expired_verification_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        $other = User::factory()->unverified()->create();

        $wrongHash = URL::temporarySignedRoute('verification.web', now()->addHour(), ['id' => $user->id, 'hash' => sha1($other->email)]);
        $this->get($wrongHash)->assertSessionHas('error');

        $expired = URL::temporarySignedRoute('verification.web', now()->subMinute(), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->get($expired)->assertSessionHas('error');

        $this->get("/verificar-correo/{$user->id}/".sha1($user->email))->assertSessionHas('error');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_reset_password_email_is_spanish_and_points_to_web_form(): void
    {
        $user = User::factory()->create(['name' => 'Luis']);
        $mail = (new ResetPassword('abc123'))->toMail($user);

        $this->assertSame('Restablece tu contraseña · SystemSeed', $mail->subject);
        $this->assertStringContainsString('/restablecer-contrasena/abc123', $mail->actionUrl);
    }

    public function test_custom_404_page(): void
    {
        $this->get('/no-existe-esta-pagina')->assertNotFound()->assertSee('Esta semilla no germinó');
        $this->get('/proyectos/no-existe')->assertNotFound()->assertSee('Explorar proyectos');
    }

    public function test_api_errors_still_return_json(): void
    {
        $this->getJson('/api/v1/projects/no-existe')->assertNotFound()->assertJsonStructure(['message']);
    }

    public function test_project_page_has_share_tags(): void
    {
        $project = Project::factory()->create([
            'title' => 'Intranet ARIB', 'slug' => 'intranet-arib', 'status' => 'published',
            'short_description' => 'Sistema interno', 'cover_image' => '/storage/uploads/projects/x.png', 'published_at' => now(),
        ]);

        $this->get('/proyectos/'.$project->slug)->assertOk()
            ->assertSee('<meta property="og:title" content="Intranet ARIB | SystemSeed">', false)
            ->assertSee('<meta property="og:description" content="Sistema interno">', false)
            ->assertSee(url('/storage/uploads/projects/x.png'), false);
    }

    public function test_draft_project_page_is_not_found(): void
    {
        Project::factory()->create(['slug' => 'borrador', 'status' => 'draft']);

        $this->get('/proyectos/borrador')->assertNotFound();
    }

    public function test_home_has_default_share_image_and_private_pages_are_noindex(): void
    {
        $this->get('/')->assertOk()->assertSee(asset('og-image.png'), false)->assertSee('rel="canonical"', false);
        $this->get('/login')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_sitemap_lists_only_published_projects(): void
    {
        Project::factory()->create(['slug' => 'publicado', 'status' => 'published', 'published_at' => now()]);
        Project::factory()->create(['slug' => 'oculto', 'status' => 'draft']);

        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('projects.show', 'publicado'), false)
            ->assertDontSee('oculto');
    }
}
