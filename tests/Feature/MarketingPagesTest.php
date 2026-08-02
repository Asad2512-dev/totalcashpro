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
        $response->assertSee('Basic Plan', false);
        $response->assertSee('Professional Plan', false);
        $response->assertSee('Request Demo', false);
    }

    public function test_marketing_section_routes_redirect_to_home_anchors(): void
    {
        $this->get(route('features'))->assertRedirect('/#features');
        $this->get(route('solutions'))->assertRedirect('/#solutions');
        $this->get(route('pricing'))->assertRedirect('/#pricing');
    }

    public function test_request_access_page_and_submission(): void
    {
        Mail::fake();

        $this->get(route('request-demo', ['plan' => 'professional']))
            ->assertOk()
            ->assertSee('Request Access', false);

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
        $this->assertSame(1, AccessRequest::query()->count());
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Sign in to your account', false);
    }

    public function test_contact_form_submission(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), [
            'name' => 'Amelia Hart',
            'email' => 'amelia@example.com',
            'phone' => '+44 7700 900111',
            'subject' => 'Plan question',
            'message' => 'Can you explain the difference between Basic and Professional?',
        ])->assertRedirect(route('contact'));
    }

    public function test_static_marketing_pages_render_successfully(): void
    {
        $this->get(route('about'))->assertOk();
        $this->get(route('contact'))->assertOk()->assertSee('Send a message', false);
        $this->get(route('privacy'))->assertOk()->assertSee('Privacy Policy', false);
        $this->get(route('terms'))->assertOk();
    }
}
