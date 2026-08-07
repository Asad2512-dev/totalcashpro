<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrganizationStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class SignupFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_renders(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Start My Free Trial', false)
            ->assertSee('Business name', false);
    }

    public function test_user_can_register_and_receive_trial_setup(): void
    {
        Mail::fake();
        Event::fake([Registered::class]);

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $response = $this->post(route('register.store'), [
            'business_name' => 'Sunrise Cafe',
            'owner_name' => 'Alex Morgan',
            'email' => 'alex@sunrisecafe.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+44 7700 900200',
            'country' => 'GB',
            'business_type' => 'cafe',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $this->assertAuthenticated();
        $user = User::query()->where('email', 'alex@sunrisecafe.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isAdmin());
        $this->assertNull($user->email_verified_at);

        $organization = Organization::query()->where('slug', 'sunrise-cafe')->first();
        $this->assertNotNull($organization);
        $this->assertSame(OrganizationStatus::Trial, $organization->status);
        $this->assertNotNull($organization->trial_ends_at);

        $branch = Branch::query()->where('organization_id', $organization->id)->first();
        $this->assertNotNull($branch);
        $this->assertSame('Main Branch', $branch->name);

        $subscription = Subscription::query()->where('organization_id', $organization->id)->first();
        $this->assertNotNull($subscription);
        $this->assertSame(SubscriptionStatus::Trialing, $subscription->status);
        $this->assertSame('professional', $subscription->plan?->slug);

        Event::assertDispatched(Registered::class);
    }

    public function test_legacy_request_demo_redirects_to_register(): void
    {
        $this->get('/request-demo')->assertRedirect(route('register'));
        $this->get('/request-access')->assertRedirect(route('register'));
    }

    public function test_homepage_shows_start_free_trial_cta(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Start Your Free 14-Day Trial', false)
            ->assertDontSee('Request Demo', false);
    }
}
