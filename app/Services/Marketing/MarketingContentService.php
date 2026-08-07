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
            ['value' => 'PIN Kiosk', 'label' => 'Attendance'],
            ['value' => 'Multi-branch', 'label' => 'Professional'],
            ['value' => 'Cloud', 'label' => 'Always online'],
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
                'description' => 'Staff panel and dedicated attendance kiosk with PIN entry — no admin access required on the floor.',
                'icon' => 'clock-in',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
            [
                'title' => 'Attendance Kiosk',
                'description' => 'Full-screen tablet terminal at your entrance. Large buttons, live clock, branch-scoped PIN clock-in.',
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
            [
                'title' => 'Finance & Accounting',
                'description' => 'Income, expenses, bills, bank accounts, cash flow and VAT summaries in one finance hub.',
                'icon' => 'cash',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'CRM & Customers',
                'description' => 'Track customer visits, notes and timelines for hospitality and retail relationships.',
                'icon' => 'users',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'HR & Leave',
                'description' => 'Leave requests, shift swaps and people workflows alongside staff management.',
                'icon' => 'roles',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Purchase Orders',
                'description' => 'Create, approve and receive purchase orders linked to suppliers and inventory.',
                'icon' => 'suppliers',
                'plan' => 'professional',
                'planLabel' => 'Professional Only',
            ],
            [
                'title' => 'Security — 2FA & OTP',
                'description' => 'Two-factor authentication, OTP verification, login history and device management.',
                'icon' => 'security',
                'plan' => 'basic',
                'planLabel' => 'All Plans',
            ],
            [
                'title' => 'Notifications & Email',
                'description' => 'In-app notifications and automated transactional email for staff and owners.',
                'icon' => 'bell',
                'plan' => 'basic',
                'planLabel' => 'Included in Basic',
            ],
        ];
    }

    /**
     * Grouped feature sections for the dedicated features page.
     *
     * @return list<array{title: string, description: string, items: list<array{title: string, description: string}>}>
     */
    public function featureCategories(): array
    {
        return [
            [
                'title' => 'Attendance Kiosk',
                'description' => 'A dedicated time-clock terminal — not another admin page.',
                'items' => [
                    ['title' => 'PIN Clock In', 'description' => '4-digit PIN keypad with automatic verification and branch scoping.'],
                    ['title' => 'Touch Terminal UI', 'description' => 'Large buttons, live clock, success animations — built for iPad and Android tablets.'],
                    ['title' => 'Break Tracking', 'description' => 'Clock in, clock out, start break and end break from one screen.'],
                    ['title' => 'Kiosk Audit Log', 'description' => 'Every clock event and failed PIN attempt is logged for managers.'],
                ],
            ],
            [
                'title' => 'Operations & Cash',
                'description' => 'Daily cash up, history and operational dashboards.',
                'items' => [
                    ['title' => 'Business Dashboard', 'description' => 'Cash, staff clocked in, inventory alerts and charts at a glance.'],
                    ['title' => 'Daily Cash Up', 'description' => 'Guided shift close with coins, notes, cards and deductions.'],
                    ['title' => 'Cash History', 'description' => 'Searchable history of every cash-up record.'],
                    ['title' => 'Staff Dashboard', 'description' => 'Separate staff panel for shifts, clock and personal attendance.'],
                ],
            ],
            [
                'title' => 'Finance & Reports',
                'description' => 'Accounting tools and export-ready reports.',
                'items' => [
                    ['title' => 'Finance Dashboard', 'description' => 'Income, expenses, bills and bank accounts in one hub.'],
                    ['title' => 'Cash Flow & P&L', 'description' => 'Cash flow statements and profit & loss reporting.'],
                    ['title' => 'Reports Center', 'description' => 'Attendance, cash, inventory and payroll reports with CSV export.'],
                    ['title' => 'Petty Cash & Drawers', 'description' => 'Petty cash accounts and cash drawer tracking.'],
                ],
            ],
            [
                'title' => 'People & Inventory',
                'description' => 'Staff, HR, rota, payroll and stock.',
                'items' => [
                    ['title' => 'Payroll & Wages', 'description' => 'Generate wages from attendance and approve payroll runs.'],
                    ['title' => 'Staff Rota', 'description' => 'Sections, groups and weekly shift planning.'],
                    ['title' => 'Inventory', 'description' => 'Categories, stock counts, adjustments and low-stock alerts.'],
                    ['title' => 'Supplier Management', 'description' => 'Suppliers, invoices and purchase order workflows.'],
                ],
            ],
            [
                'title' => 'Platform & Security',
                'description' => 'Multi-branch, CRM, notifications and enterprise-grade auth.',
                'items' => [
                    ['title' => 'Multi-Branch', 'description' => 'Filter dashboards and kiosk by branch as you grow.'],
                    ['title' => 'CRM', 'description' => 'Customer profiles, visits, notes and timeline.'],
                    ['title' => '2FA & OTP', 'description' => 'Email OTP and two-factor authentication for sensitive actions.'],
                    ['title' => 'Email Automation', 'description' => 'Password resets, invites and notification emails via SMTP.'],
                ],
            ],
        ];
    }

    /**
     * Employee day workflow for marketing visual.
     *
     * @return list<array{step: string, title: string, description: string}>
     */
    public function employeeWorkflow(): array
    {
        return [
            ['step' => '01', 'title' => 'Employee Arrives', 'description' => 'Staff walk to the attendance kiosk at your entrance — no login required.'],
            ['step' => '02', 'title' => 'PIN Clock In', 'description' => 'Enter a 4-digit PIN. Name, photo and today\'s status appear instantly.'],
            ['step' => '03', 'title' => 'Attendance', 'description' => 'Hours, breaks and rota rules feed accurate attendance records.'],
            ['step' => '04', 'title' => 'Payroll', 'description' => 'Managers approve wages generated from verified clock data.'],
            ['step' => '05', 'title' => 'Finance', 'description' => 'Labour and cash data flow into finance, cash flow and P&L.'],
            ['step' => '06', 'title' => 'Reports', 'description' => 'Export attendance, cash and profit reports for your accountant.'],
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
                'title' => 'Attendance Kiosk',
                'description' => 'Dedicated PIN terminal for tablets at your entrance — a key selling point for busy restaurants.',
                'icon' => 'clock-in',
            ],
            [
                'title' => 'Fast & Cloud-Based',
                'description' => 'Instant signup, no local install, works on desktop, tablet and phone from anywhere.',
                'icon' => 'fast',
            ],
            [
                'title' => 'Secure by Design',
                'description' => '2FA, OTP, login history, device management and role-based access across all panels.',
                'icon' => 'security',
            ],
            [
                'title' => 'Multi-Branch Ready',
                'description' => 'Harbour Central, Dockside or every branch — filter operations and kiosk by location.',
                'icon' => 'branches',
            ],
            [
                'title' => 'Professional Reports',
                'description' => 'Cash flow, profit & loss, attendance and inventory exports your accountant will appreciate.',
                'icon' => 'analytics',
            ],
            [
                'title' => 'Staff Management',
                'description' => 'Staff profiles, PIN codes, rota, HR leave and payroll in one people hub.',
                'icon' => 'roles',
            ],
            [
                'title' => 'Finance Built In',
                'description' => 'Income, expenses, bills, suppliers and petty cash — not a bolt-on spreadsheet.',
                'icon' => 'cash',
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
                    'Attendance Kiosk',
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
            [
                'name' => 'Enterprise',
                'price' => 'Custom',
                'period' => '',
                'badge' => 'Coming Soon',
                'popularBadge' => null,
                'description' => 'Multi-site groups, custom integrations, dedicated support and advanced compliance.',
                'cta' => 'Contact Sales',
                'featured' => false,
                'ctaHref' => route('contact'),
                'assurances' => [
                    'Volume Pricing',
                    'Dedicated Onboarding',
                    'SLA Options',
                ],
                'features' => [
                    'Everything in Professional',
                    'Multi-Organisation Groups',
                    'Custom Integrations',
                    'Advanced API Access',
                    'Dedicated Account Manager',
                    'Priority SLA Support',
                    'Custom Reporting',
                    'SSO (planned)',
                ],
            ],
        ];
    }

    /**
     * @return list<array{feature: string, basic: bool|string, professional: bool|string, enterprise: bool|string}>
     */
    public function pricingComparison(): array
    {
        return [
            ['feature' => 'Daily Cash Up', 'basic' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Attendance Kiosk (PIN)', 'basic' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Staff Panel', 'basic' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Up to 5 Staff', 'basic' => true, 'professional' => 'Unlimited', 'enterprise' => 'Unlimited'],
            ['feature' => 'Suppliers & Invoices', 'basic' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Inventory Management', 'basic' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Payroll & Wages', 'basic' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Finance & Accounting', 'basic' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'CRM', 'basic' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Multiple Branches', 'basic' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Reports Export', 'basic' => 'Daily', 'professional' => 'Unlimited', 'enterprise' => 'Custom'],
            ['feature' => '2FA & OTP Security', 'basic' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Priority Support', 'basic' => false, 'professional' => true, 'enterprise' => 'Dedicated'],
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
                'question' => 'What is the Attendance Kiosk?',
                'answer' => 'A dedicated full-screen clock terminal for tablets at your restaurant entrance. Staff enter a PIN to clock in or out — they never see the business admin panel. Owners launch kiosk mode once and exit with their admin password.',
            ],
            [
                'question' => 'Does TotalCashPro include finance and accounting?',
                'answer' => 'Professional includes a finance hub with income, expenses, bills, bank accounts, cash flow, profit & loss, VAT summaries, petty cash and recurring bills.',
            ],
            [
                'question' => 'Can I manage inventory and purchase orders?',
                'answer' => 'Yes on Professional. Track categories, stock counts, adjustments, history and create purchase orders linked to suppliers.',
            ],
            [
                'question' => 'How does payroll work?',
                'answer' => 'Attendance from the kiosk and staff panel feeds wage calculations. Managers generate, approve and mark payroll runs as paid.',
            ],
            [
                'question' => 'Is my data secure?',
                'answer' => 'TotalCashPro includes 2FA, OTP verification, login history, device management, security logs and role-based access for Super Admin, Business Admin and Staff panels.',
            ],
            [
                'question' => 'Can I run multiple branches?',
                'answer' => 'Professional supports unlimited branches with branch filters across dashboard, reports and attendance kiosk.',
            ],
            [
                'question' => 'How do I get started?',
                'answer' => 'Click Start Free Trial, complete registration, verify your email, and follow the welcome wizard into your Business Admin dashboard.',
            ],
        ];
    }
}
