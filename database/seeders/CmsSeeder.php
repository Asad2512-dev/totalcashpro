<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PublishStatus;
use App\Models\CmsFaq;
use App\Models\CmsFeature;
use App\Models\CmsHeroSection;
use App\Models\CmsPage;
use App\Models\CmsTestimonial;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

final class CmsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['title' => 'Home', 'slug' => 'home'],
            ['title' => 'About', 'slug' => 'about'],
            ['title' => 'Contact', 'slug' => 'contact'],
            ['title' => 'Privacy', 'slug' => 'privacy'],
            ['title' => 'Terms', 'slug' => 'terms'],
        ] as $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'status' => PublishStatus::Published,
                    'published_at' => now(),
                ],
            );
        }

        CmsHeroSection::query()->updateOrCreate(
            ['page_key' => 'home', 'sort_order' => 1],
            [
                'eyebrow' => 'Cloud software for restaurants & retail',
                'headline' => 'Manage cash, staff and reports from one secure dashboard',
                'subheadline' => 'Built for restaurants, cafés, takeaways and retail businesses.',
                'primary_cta_label' => 'Request Demo',
                'primary_cta_url' => '/request-demo',
                'secondary_cta_label' => 'Choose Your Plan',
                'secondary_cta_url' => '/#pricing',
                'status' => PublishStatus::Published,
            ],
        );

        $features = [
            ['title' => 'Daily Cash Up', 'plan_slug' => 'basic', 'sort_order' => 1],
            ['title' => 'Staff Clock In & Out', 'plan_slug' => 'basic', 'sort_order' => 2],
            ['title' => 'Inventory Management', 'plan_slug' => 'professional', 'sort_order' => 9],
            ['title' => 'Payroll & Wages', 'plan_slug' => 'professional', 'sort_order' => 10],
        ];

        foreach ($features as $feature) {
            CmsFeature::query()->updateOrCreate(
                ['title' => $feature['title']],
                [
                    'description' => $feature['title'].' for TotalCashPro customers.',
                    'plan_slug' => $feature['plan_slug'],
                    'sort_order' => $feature['sort_order'],
                    'status' => PublishStatus::Published,
                ],
            );
        }

        CmsTestimonial::query()->updateOrCreate(
            ['name' => 'Amelia Hart', 'business' => 'Northbridge Kitchen'],
            [
                'role' => 'Operations Manager',
                'quote' => 'Cash ups and staff attendance finally live in one place.',
                'is_featured' => true,
                'sort_order' => 1,
                'status' => PublishStatus::Published,
            ],
        );

        $faqs = [
            ['question' => 'How much does TotalCashPro cost?', 'answer' => 'Basic is £19.99/month. Professional is £29.99/month.', 'sort_order' => 1],
            ['question' => 'Can I sign up instantly?', 'answer' => 'No. Submit a request and our team reviews it before creating your account.', 'sort_order' => 2],
        ];

        foreach ($faqs as $faq) {
            CmsFaq::query()->updateOrCreate(
                ['question' => $faq['question']],
                [
                    'answer' => $faq['answer'],
                    'sort_order' => $faq['sort_order'],
                    'status' => PublishStatus::Published,
                ],
            );
        }

        $templates = [
            ['slug' => 'access-credentials', 'name' => 'Access credentials', 'subject' => 'Your TotalCashPro login details', 'body' => 'Your account has been created. Email: {{email}} Password: {{password}}', 'trigger' => 'Account created'],
            ['slug' => 'welcome', 'name' => 'Welcome Email', 'subject' => 'Welcome to TotalCashPro', 'body' => 'Welcome aboard, {{name}}.', 'trigger' => 'Welcome'],
            ['slug' => 'reset-password', 'name' => 'Reset Password', 'subject' => 'Reset your TotalCashPro password', 'body' => 'Use this link to reset your password: {{url}}', 'trigger' => 'Password reset'],
            ['slug' => 'trial-started', 'name' => 'Trial Started', 'subject' => 'Your trial has started', 'body' => 'Your {{plan}} trial is active until {{ends_at}}.', 'trigger' => 'Trial started'],
            ['slug' => 'trial-ending', 'name' => 'Trial Ending', 'subject' => 'Your trial ends soon', 'body' => 'Your trial ends on {{ends_at}}. Upgrade to keep access.', 'trigger' => 'Trial ending'],
            ['slug' => 'subscription-active', 'name' => 'Subscription Active', 'subject' => 'Subscription activated', 'body' => 'Your {{plan}} subscription is now active.', 'trigger' => 'Subscription active'],
            ['slug' => 'subscription-expired', 'name' => 'Subscription Expired', 'subject' => 'Subscription expired', 'body' => 'Your subscription has expired. Renew to restore access.', 'trigger' => 'Subscription expired'],
            ['slug' => 'invoice', 'name' => 'Invoice', 'subject' => 'Your invoice {{invoice}}', 'body' => 'Invoice {{invoice}} for {{amount}} is attached.', 'trigger' => 'Invoice'],
            ['slug' => 'payment-success', 'name' => 'Payment Success', 'subject' => 'Payment received', 'body' => 'We received your payment of {{amount}}.', 'trigger' => 'Payment success'],
        ];

        foreach ($templates as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['slug' => $template['slug']],
                [
                    'name' => $template['name'],
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'trigger' => $template['trigger'],
                    'locale' => 'en',
                    'status' => PublishStatus::Published,
                ],
            );
        }
    }
}
