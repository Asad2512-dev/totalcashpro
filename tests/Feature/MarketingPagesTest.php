<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class MarketingPagesTest extends TestCase
{
    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('TotalCashPro', false);
        $response->assertSee('Own Your Business Software for Just £29', false);
        $response->assertSee('Buy Now – £29', false);
        $response->assertSee('Lifetime License', false);
        $response->assertSee('No subscriptions. No recurring charges.', false);
        $response->assertDontSee('/month', false);
    }

    public function test_static_marketing_pages_render_successfully(): void
    {
        $this->get(route('about'))->assertOk()->assertSee('£29', false);
        $this->get(route('contact'))->assertOk()->assertSee('Contact', false);
        $this->get(route('privacy'))->assertOk()->assertSee('Privacy Policy', false);
        $this->get(route('terms'))->assertOk()->assertSee('one-time Lifetime License', false);
    }

    public function test_purchase_placeholders_redirect_to_buy_anchor(): void
    {
        $this->get(route('login'))->assertRedirect('/#buy');
        $this->get(route('register'))->assertRedirect('/#buy');
        $this->get(route('buy'))->assertRedirect('/#buy');
    }
}
