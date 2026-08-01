<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AccessRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class MarketingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('TotalCashPro', false);
        $response->assertSee('Manage cash, staff and reports from one secure dashboard', false);
        $response->assertSee('Basic Plan', false);
        $response->assertSee('Professional Plan', false);
        $response->assertSee('£19.99', false);
        $response->assertSee('£29.99', false);
        $response->assertSee('/month', false);
        $response->assertSee('Request Professional Plan', false);
        $response->assertSee('Monthly Subscription', false);
        $response->assertSee('Choose Your Plan', false);
        $response->assertSee('Submit Business Request', false);
        $response->assertDontSee('Lifetime', false);
        $response->assertDontSee('One-Time', false);
        $response->assertDontSee('Start Free Trial', false);
    }

    public function test_request_access_page_and_submission(): void
    {
        Mail::fake();

        $this->get(route('request-access', ['plan' => 'professional']))
            ->assertOk()
            ->assertSee('Request Access', false)
            ->assertSee('Submit Request', false);

        $response = $this->post(route('request-access.store'), [
            'business_name' => 'Harbour Retail',
            'owner_name' => 'Daniel Okoye',
            'email' => 'daniel@example.com',
            'phone' => '+44 7700 900123',
            'business_address' => '12 Harbour Road',
            'country' => 'United Kingdom',
            'business_type' => 'Retail Store',
            'number_of_employees' => '6-15',
            'selected_plan' => 'professional',
            'additional_notes' => 'Two branches',
        ]);

        $response->assertRedirect(route('request-access.thanks'));

        $this->assertDatabaseHas('access_requests', [
            'business_name' => 'Harbour Retail',
            'email' => 'daniel@example.com',
            'selected_plan' => 'professional',
            'status' => 'pending',
        ]);

        $this->assertSame(1, AccessRequest::query()->count());
    }

    public function test_static_marketing_pages_render_successfully(): void
    {
        $this->get(route('about'))->assertOk()->assertSee('£19.99/month', false);
        $this->get(route('contact'))->assertOk()->assertSee('Contact', false);
        $this->get(route('privacy'))->assertOk()->assertSee('Privacy Policy', false);
        $this->get(route('terms'))->assertOk()->assertSee('£19.99/month', false);
    }

    public function test_purchase_placeholders_redirect_to_request_access(): void
    {
        $this->get(route('login'))->assertRedirect('/request-access');
        $this->get(route('register'))->assertRedirect('/request-access');
        $this->get(route('buy'))->assertRedirect('/request-access');
    }
}
