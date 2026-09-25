<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SecurityDeploymentReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_example_environment_contains_only_safe_placeholders(): void
    {
        $env = $this->parseEnvExample();

        $this->assertSame('', $env['APP_KEY'] ?? null);
        $this->assertSame('sqlite', $env['DB_CONNECTION'] ?? null);
        $this->assertSame('', $env['LOCAL_DEV_ADMIN_NAME'] ?? null);
        $this->assertSame('', $env['LOCAL_DEV_ADMIN_EMAIL'] ?? null);
        $this->assertSame('', $env['LOCAL_DEV_ADMIN_PASSWORD'] ?? null);
        $this->assertSame('', $env['AWS_ACCESS_KEY_ID'] ?? null);
        $this->assertSame('', $env['AWS_SECRET_ACCESS_KEY'] ?? null);
        $this->assertArrayNotHasKey('DB_PASSWORD', $env);
    }

    public function test_cors_configuration_is_explicit_and_credentialed_without_wildcards(): void
    {
        $env = $this->parseEnvExample();
        $origins = array_filter(array_map('trim', explode(',', $env['CORS_ALLOWED_ORIGINS'] ?? '')));

        $this->assertIsArray($origins);
        $this->assertNotEmpty($origins);
        $this->assertNotContains('*', $origins);
        $this->assertTrue(config('cors.supports_credentials'));
    }

    public function test_public_write_cookie_and_response_headers_preserve_visitor_privacy(): void
    {
        config(['portfolio.visitor.hash_secret' => 'security-review-secret']);

        Project::factory()->published()->create(['slug' => ['en' => 'security-review-project']]);

        $response = $this->postJson('/api/v1/projects/security-review-project/views');

        $response->assertCreated()
            ->assertJsonMissing(['visitor_id_hash'])
            ->assertJsonMissing(['ip_hash'])
            ->assertJsonMissing(['user_agent_hash']);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === config('portfolio.visitor.cookie', 'portfolio_visitor'));

        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', strtolower((string) $cookie->getSameSite()));
    }

    public function test_public_uploads_and_private_columns_remain_blocked(): void
    {
        foreach (['project_views', 'project_likes', 'analytics_events'] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'ip_address'));
            $this->assertFalse(Schema::hasColumn($table, 'raw_ip'));
        }

        $this->postJson('/api/v1/contact', [
            'attachment' => 'not-supported',
            'email' => 'qa@example.test',
            'message' => 'I would like to discuss a security review for this portfolio.',
            'name' => 'QA Reviewer',
            'privacy_consent' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors('attachment');

        $this->assertNotContains('image/svg+xml', config('portfolio.media.image_mimes'));
        $this->assertNotContains('text/html', config('portfolio.media.image_mimes'));
        $this->assertNotContains('application/x-msdownload', config('portfolio.media.video_mimes'));
    }

    /**
     * @return array<string, string>
     */
    private function parseEnvExample(): array
    {
        $path = base_path('.env.example');
        $values = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $values[$key] = trim($value, "\"'");
        }

        return $values;
    }
}
