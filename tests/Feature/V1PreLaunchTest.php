<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class V1PreLaunchTest extends TestCase
{
    use RefreshDatabase;

    public static function errorViewProvider(): array
    {
        return [
            '403' => ['errors.403', 'Access denied'],
            '404' => ['errors.404', 'Page not found'],
            '419' => ['errors.419', 'Session expired'],
            '422' => ['errors.422', 'Invalid request'],
            '429' => ['errors.429', 'Too many requests'],
            '500' => ['errors.500', 'Something went wrong'],
            '503' => ['errors.503', 'Service unavailable'],
        ];
    }

    #[DataProvider('errorViewProvider')]
    public function test_custom_error_views_render(string $view, string $titleFragment): void
    {
        $html = view($view)->render();

        $this->assertStringContainsString($titleFragment, $html);
    }

    public function test_application_returns_custom_404_page(): void
    {
        $this->get('/route-that-does-not-exist-v1')
            ->assertNotFound()
            ->assertSee('Page not found', false);
    }

    public function test_staff_pwa_assets_exist_on_disk(): void
    {
        $this->assertFileExists(public_path('staff-manifest.webmanifest'));
        $this->assertFileExists(public_path('staff-sw.js'));
    }

    public function test_health_endpoint_is_up(): void
    {
        $this->get('/up')->assertOk();
    }
}
