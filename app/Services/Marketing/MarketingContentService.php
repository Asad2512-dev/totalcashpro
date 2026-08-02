<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Contracts\ServiceInterface;

/**
 * Structured marketing content for the public website.
 */
final class MarketingContentService implements ServiceInterface
{
    /**
     * @return list<array{value: string, label: string}>
     */
    public function heroStats(): array
    {
        return [
            ['value' => '14 days', 'label' => 'Free trial'],
            ['value' => '£19.99', 'label' => 'Basic / month'],
            ['value' => '£29.99', 'label' => 'Pro / month'],
            ['value' => 'Instant', 'label' => 'Signup'],
        ];
    }

    /**
     * @return list<string>
     */
    public function trustedIndustries(): array
    {
        return [
            'Restaurants',
            'Cafés',
            'Takeaways',
            'Food Trucks',
            'Retail',
            'Salons',
            'Bakeries',
            'Pharmacies',
        ];
    }

    /**
     * @return list<array{title: string, description: string, icon: string, plan: string, planLabel: string}>
     */
    public function features(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'description' => 'See cash, staff and daily activity in one clear view when you open the app.',
                'icon' => 'analytics',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Daily Cash Up',
                'description' => 'Close each shift with a guided cash reconciliation and a clear record of the day.',
                'icon' => 'cash',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Cash History',
                'description' => 'Look back at previous cash ups whenever you need to check totals or resolve questions.',
                'icon' => 'reports',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Staff Clock In & Out',
                'description' => 'Let staff start and end shifts quickly with a simple clock-in and clock-out flow.',
                'icon' => 'clock-in',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Attendance',
                'description' => 'Keep accurate attendance records ready for managers and end-of-week reviews.',
                'icon' => 'attendance',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Up to 5 Staff Members',
                'description' => 'Manage a small team with staff profiles, roles and attendance on the Basic plan.',
                'icon' => 'roles',
                'plan' => 'basic',
                'planLabel' => 'Basic',
            ],
            [
                'title' => 'Suppliers & Invoices',
                'description' => 'Store supplier details and invoices in one place instead of scattered paperwork.',
                'icon' => 'suppliers',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Daily Reports',
                'description' => 'Review daily operations with clear reports that help you see what needs attention.',
                'icon' => 'reports',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Unlimited Staff',
                'description' => 'Add as many team members as you need — no staff limit on Professional.',
                'icon' => 'roles',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Inventory Management',
                'description' => 'Track stock levels, categories, adjustments and history so shelves stay prepared.',
                'icon' => 'inventory',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Staff Rota & Shifts',
                'description' => 'Plan rotas and shifts so managers know who is working before the day starts.',
                'icon' => 'attendance',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Payroll & Wages',
                'description' => 'Turn attendance into payroll-ready summaries without juggling separate spreadsheets.',
                'icon' => 'payroll',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Multiple Branches',
                'description' => 'Manage more than one location from the same Professional subscription as you grow.',
                'icon' => 'branches',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Advanced Reports & Analytics',
                'description' => 'Export reports, review profit & loss, and spot trends across cash, labour and stock.',
                'icon' => 'analytics',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
        ];
    }

    /**
     * @return list<array{step: string, title: string, description: string}>
     */
    public function workflow(): array
    {
        return [
            [
                'step' => '01',
                'title' => 'Create Account',
                'description' => 'Sign up with your business details and start instantly — no approval wait.',
            ],
            [
                'step' => '02',
                'title' => 'Verify Email',
                'description' => 'Confirm your email address to secure your account.',
            ],
            [
                'step' => '03',
                'title' => 'Create Business',
                'description' => 'Your organisation and Main Branch are created automatically.',
            ],
            [
                'step' => '04',
                'title' => 'Automatic Trial',
                'description' => 'Enjoy 14 days of Professional features — no payment required.',
            ],
            [
                'step' => '05',
                'title' => 'Start Managing',
                'description' => 'Open your dashboard and run cash up, staff, attendance and more.',
            ],
        ];
    }

    /**
     * @return list<array{title: string, description: string, accent: string}>
     */
    public function industries(): array
    {
        return [
            ['title' => 'Restaurants', 'description' => 'Keep cash close, staff time and daily reports organised across service periods.', 'accent' => 'royal'],
            ['title' => 'Cafés', 'description' => 'Manage busy shifts with clear attendance, cash ups and end-of-day totals.', 'accent' => 'emerald'],
            ['title' => 'Takeaways', 'description' => 'Close the till cleanly and keep supplier invoices where you can find them.', 'accent' => 'sky'],
            ['title' => 'Food Trucks', 'description' => 'Run lean operations with cloud software that stays simple on the move.', 'accent' => 'royal'],
            ['title' => 'Retail Stores', 'description' => 'Bring staffing, suppliers and reporting into one practical system.', 'accent' => 'emerald'],
            ['title' => 'Pharmacies', 'description' => 'Maintain accountable workflows with secure login and clear daily records.', 'accent' => 'sky'],
            ['title' => 'Salons', 'description' => 'Track teams, attendance and day-to-day operations without extra tools.', 'accent' => 'royal'],
            ['title' => 'Bakeries', 'description' => 'Keep staff time, closing routines and supplier records under control.', 'accent' => 'emerald'],
        ];
    }

    /**
     * @return list<array{title: string, description: string, icon: string}>
     */
    public function whyChoose(): array
    {
        return [
            [
                'title' => 'Cloud SaaS Access',
                'description' => 'Use TotalCashPro securely online from desktop, tablet or phone — no local install required.',
                'icon' => 'cloud',
            ],
            [
                'title' => 'Better Than Spreadsheets',
                'description' => 'Replace scattered notes and sheets with one secure dashboard your team can use.',
                'icon' => 'easy',
            ],
            [
                'title' => 'Two Clear Plans',
                'description' => 'Start with Basic for cash and staff, or choose Professional for inventory, payroll and branches.',
                'icon' => 'reports',
            ],
            [
                'title' => 'Instant Signup',
                'description' => 'Create your account in minutes with a 14-day Professional trial — no manual approval.',
                'icon' => 'security',
            ],
            [
                'title' => 'Built for Real Businesses',
                'description' => 'Designed for restaurants, cafés, takeaways and retail — not generic office software.',
                'icon' => 'fast',
            ],
            [
                'title' => 'Clear Daily Reports',
                'description' => 'Understand cash and staff activity with reports that are easy to read.',
                'icon' => 'analytics',
            ],
            [
                'title' => 'Cancel Anytime',
                'description' => 'Monthly subscriptions stay flexible. Keep access while you need it.',
                'icon' => 'affordable',
            ],
            [
                'title' => 'Priority Paths Available',
                'description' => 'Professional includes priority support when your team needs faster help.',
                'icon' => 'reliable',
            ],
        ];
    }

    /**
     * @return list<array{
     *     name: string,
     *     price: string,
     *     period: string,
     *     badge: string,
     *     popularBadge: string|null,
     *     description: string,
     *     cta: string,
     *     features: list<string>,
     *     assurances: list<string>,
     *     featured: bool,
     *     ctaHref: string
     * }>
     */
    public function pricingPlans(): array
    {
        return [
            [
                'name' => 'Basic Plan',
                'price' => '£19.99',
                'period' => '/month',
                'badge' => 'Monthly Subscription',
                'popularBadge' => null,
                'description' => 'Perfect for small restaurants, cafés, takeaways and retail businesses.',
                'cta' => 'Start Free Trial',
                'featured' => false,
                'ctaHref' => route('register'),
                'assurances' => [
                    '14-Day Free Trial',
                    'No Setup Fees',
                    'Cancel Anytime',
                ],
                'features' => [
                    'Dashboard',
                    'Daily Cash Up',
                    'Cash History',
                    'Reports',
                    'Up to 5 Staff Members',
                    'Staff Clock In',
                    'Staff Clock Out',
                    'Attendance',
                    'Staff Management',
                    'Suppliers',
                    'Supplier Invoices',
                    'Business Profile',
                    'Secure Login',
                    'Daily Reports',
                    'Email Support',
                    'Responsive Dashboard',
                ],
            ],
            [
                'name' => 'Professional Plan',
                'price' => '£29.99',
                'period' => '/month',
                'badge' => 'Monthly Subscription',
                'popularBadge' => 'MOST POPULAR',
                'description' => 'Everything included in Basic plus complete business management tools.',
                'cta' => 'Start Free Trial',
                'featured' => true,
                'ctaHref' => route('register'),
                'assurances' => [
                    '14-Day Free Trial',
                    'No Setup Fees',
                    'Cancel Anytime',
                ],
                'features' => [
                    'Everything in Basic',
                    'Unlimited Staff',
                    'Unlimited Reports',
                    'Inventory Management',
                    'Inventory Categories',
                    'Inventory History',
                    'Stock Count',
                    'Stock Adjustments',
                    'Low Stock Alerts',
                    'Stock Notifications',
                    'Staff Rota',
                    'Shift Planning',
                    'Multiple Branches',
                    'Advanced Dashboard',
                    'Profit & Loss Reports',
                    'Payroll',
                    'Wages Management',
                    'Attendance Reports',
                    'Export Reports',
                    'Advanced Analytics',
                    'Priority Support',
                ],
            ],
        ];
    }

    /**
     * @return list<array{quote: string, name: string, role: string, business: string}>
     */
    public function testimonials(): array
    {
        return [
            [
                'quote' => 'We signed up in minutes, verified email, and had cash up and attendance running the same day.',
                'name' => 'Amelia Hart',
                'role' => 'Operations Manager',
                'business' => 'Northbridge Kitchen',
            ],
            [
                'quote' => 'The 14-day Professional trial let us test inventory and payroll before choosing a plan.',
                'name' => 'Daniel Okoye',
                'role' => 'Owner',
                'business' => 'Harbour Retail Group',
            ],
            [
                'quote' => 'Instant signup, automatic branch setup, and a clear dashboard — exactly what our managers needed.',
                'name' => 'Sofia Mendes',
                'role' => 'Regional Director',
                'business' => 'Lumen Coffee Co.',
            ],
        ];
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    public function faqs(): array
    {
        return [
            [
                'question' => 'How much does TotalCashPro cost?',
                'answer' => 'Basic is £19.99 per month. Professional is £29.99 per month. Both are cloud SaaS subscriptions.',
            ],
            [
                'question' => 'Can I sign up instantly?',
                'answer' => 'Yes. Create your account in minutes, verify your email, and your organisation with a Main Branch and 14-day Professional trial is set up automatically.',
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Every new customer receives a 14-day Professional trial with full feature access. No credit card is required at signup.',
            ],
            [
                'question' => 'What is included in the Basic plan?',
                'answer' => 'Basic includes dashboard, daily cash up, cash history, reports, up to 5 staff, clock in/out, attendance, staff management, suppliers, supplier invoices, business profile, secure login, daily reports, email support and a responsive dashboard.',
            ],
            [
                'question' => 'What is included in the Professional plan?',
                'answer' => 'Professional includes everything in Basic, plus unlimited staff, unlimited reports, inventory tools, staff rota, shift planning, multiple branches, advanced dashboard, profit & loss, payroll, wages, attendance reports, export reports, advanced analytics and priority support.',
            ],
            [
                'question' => 'How long does account setup take?',
                'answer' => 'Setup is instant. After registration your business, branch, and trial are created automatically and you can start using the dashboard immediately.',
            ],
            [
                'question' => 'Who is TotalCashPro for?',
                'answer' => 'It is built for restaurants, cafés, takeaways, food trucks, retail stores, pharmacies, salons, bakeries and similar businesses that need practical daily operations software.',
            ],
            [
                'question' => 'Can I cancel anytime?',
                'answer' => 'Yes. Subscriptions are monthly and can be cancelled anytime. You keep access while your subscription is active.',
            ],
            [
                'question' => 'How do I get started?',
                'answer' => 'Click Start Free Trial, complete registration, verify your email, and follow the welcome wizard into your Business Admin dashboard.',
            ],
        ];
    }
}
