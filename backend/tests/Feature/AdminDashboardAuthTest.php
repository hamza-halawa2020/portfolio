<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('livewire-rate-limiter:'.sha1(Login::class.'|authenticate|127.0.0.1'));
    }

    public function test_guest_dashboard_access_redirects_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_is_noindexed_private_and_has_no_registration_route(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));

        $this->get('/admin/register')->assertNotFound();
    }

    public function test_admin_login_supports_english_ltr_and_arabic_rtl(): void
    {
        $this->get('/admin/login?locale=en')
            ->assertOk()
            ->assertSee('dir="ltr"', false);

        $this->get('/admin/login?locale=ar')
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }

    public function test_authorized_administrator_can_login_and_access_dashboard(): void
    {
        $admin = User::factory()->administrator()->create([
            'email' => 'owner@example.test',
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'owner@example.test',
                'password' => 'correct-password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($admin);

        $response = $this->get('/admin');

        $response->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));
    }

    public function test_ordinary_authenticated_user_is_denied_panel_access(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_public_visitor_cookie_is_not_an_admin_authentication_credential(): void
    {
        $this->withCredentials()
            ->withUnencryptedCookie((string) config('portfolio.visitor.cookie', 'portfolio_visitor'), Crypt::encryptString('visitor-only'))
            ->get('/admin')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_incorrect_credentials_use_generic_failure_and_do_not_authenticate(): void
    {
        User::factory()->administrator()->create([
            'email' => 'owner@example.test',
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'owner@example.test',
                'password' => 'wrong-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_login_throttling_prevents_repeated_attempts(): void
    {
        User::factory()->administrator()->create([
            'email' => 'owner@example.test',
            'password' => Hash::make('correct-password'),
        ]);

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            Livewire::test(Login::class)
                ->fillForm([
                    'email' => 'owner@example.test',
                    'password' => 'wrong-password',
                ])
                ->call('authenticate');
        }

        $this->assertTrue(RateLimiter::tooManyAttempts(
            'livewire-rate-limiter:'.sha1(Login::class.'|authenticate|127.0.0.1'),
            5,
        ));
        $this->assertGuest();
    }

    public function test_logout_invalidates_admin_session(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_owner_administrator_can_be_provisioned_interactively(): void
    {
        $this->artisan('portfolio:provision-owner-admin')
            ->expectsQuestion('Owner name', 'Fictional Owner')
            ->expectsQuestion('Owner email', 'owner@example.test')
            ->expectsQuestion('Owner password', 'very-secure-password')
            ->expectsQuestion('Confirm owner password', 'very-secure-password')
            ->assertExitCode(0);

        $owner = User::query()->where('email', 'owner@example.test')->firstOrFail();

        $this->assertTrue($owner->is_admin);
        $this->assertNotNull($owner->admin_granted_at);
        $this->assertTrue(Hash::check('very-secure-password', $owner->password));
    }

    public function test_existing_business_policies_require_explicit_administrator(): void
    {
        $ordinaryUser = User::factory()->create();
        $admin = User::factory()->administrator()->create();
        $project = Project::factory()->published()->create();

        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('viewAny', Project::class));
        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('update', $project));
        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Project::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $project));
    }
}
