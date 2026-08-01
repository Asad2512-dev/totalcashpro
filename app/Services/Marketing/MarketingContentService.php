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
            ['value' => '£29', 'label' => 'One-time price'],
            ['value' => '12+', 'label' => 'Core tools included'],
            ['value' => '0', 'label' => 'Monthly fees'],
            ['value' => '1', 'label' => 'Simple license'],
        ];
    }

    /**
     * @return list<string>
     */
    public function trustedIndustries(): array
    {
        return [
            'Restaurants',
            'Retail',
            'Cafe',
            'Salon',
            'Bakery',
            'Pharmacy',
            'Mini Mart',
        ];
    }

    /**
     * @return list<array{title: string, description: string, icon: string}>
     */
    public function features(): array
    {
        return [
            ['title' => 'Cash Up', 'description' => 'Close each shift with a guided cash reconciliation process and a clear record of what happened.', 'icon' => 'cash'],
            ['title' => 'Attendance', 'description' => 'Keep accurate staff attendance records that are ready when you need payroll or reports.', 'icon' => 'attendance'],
            ['title' => 'Clock In', 'description' => 'Let staff start shifts quickly with a simple clock-in flow designed for busy floors.', 'icon' => 'clock-in'],
            ['title' => 'Clock Out', 'description' => 'End shifts cleanly and keep time records organised for managers and payroll.', 'icon' => 'clock-out'],
            ['title' => 'Payroll', 'description' => 'Turn attendance into payroll-ready summaries without juggling separate spreadsheets.', 'icon' => 'payroll'],
            ['title' => 'Inventory', 'description' => 'Track stock levels, movements, and low-stock alerts so shelves stay prepared.', 'icon' => 'inventory'],
            ['title' => 'Suppliers', 'description' => 'Keep supplier details and purchase history in one place for easier ordering.', 'icon' => 'suppliers'],
            ['title' => 'Reports', 'description' => 'Review daily operations with clear reports that help you see what needs attention.', 'icon' => 'reports'],
            ['title' => 'Analytics', 'description' => 'Spot trends across cash, labour, and inventory without rebuilding charts by hand.', 'icon' => 'analytics'],
            ['title' => 'Branches', 'description' => 'Manage more than one location from the same software when your business grows.', 'icon' => 'branches'],
            ['title' => 'Security', 'description' => 'Protect business data with role-based access and secure handling of sensitive records.', 'icon' => 'security'],
            ['title' => 'Role Management', 'description' => 'Give owners, managers, and staff the access they need — and nothing they do not.', 'icon' => 'roles'],
        ];
    }

    /**
     * @return list<array{step: string, title: string, description: string}>
     */
    public function workflow(): array
    {
        return [
            ['step' => '01', 'title' => 'Create Business', 'description' => 'Set up your business details and start with a clean operating structure.'],
            ['step' => '02', 'title' => 'Invite Staff', 'description' => 'Add your team and assign roles that match how you run the floor.'],
            ['step' => '03', 'title' => 'Daily Operations', 'description' => 'Handle clock-ins, stock checks, and supplier tasks as the day unfolds.'],
            ['step' => '04', 'title' => 'Cash Up', 'description' => 'Close each shift with a guided reconciliation your team can follow.'],
            ['step' => '05', 'title' => 'Reports', 'description' => 'Review daily reports to understand cash, attendance, and stock clearly.'],
            ['step' => '06', 'title' => 'Growth', 'description' => 'Keep using the same license as your operations become more organised.'],
        ];
    }

    /**
     * @return list<array{title: string, description: string, accent: string}>
     */
    public function industries(): array
    {
        return [
            ['title' => 'Restaurants', 'description' => 'Keep cash close, staff time, and inventory organised across service periods.', 'accent' => 'royal'],
            ['title' => 'Coffee Shops', 'description' => 'Manage busy shifts with clear attendance, stock, and end-of-day totals.', 'accent' => 'emerald'],
            ['title' => 'Retail Stores', 'description' => 'Bring staffing, suppliers, and reporting into one practical system.', 'accent' => 'sky'],
            ['title' => 'Pharmacies', 'description' => 'Maintain accountable workflows with clear roles and operational records.', 'accent' => 'royal'],
            ['title' => 'Salons', 'description' => 'Track teams, attendance, and day-to-day operations without extra tools.', 'accent' => 'emerald'],
            ['title' => 'Food Trucks', 'description' => 'Run lean operations with software that stays simple on the move.', 'accent' => 'sky'],
            ['title' => 'Convenience Stores', 'description' => 'Simplify cash and stock visibility for fast-moving retail days.', 'accent' => 'royal'],
            ['title' => 'Bakeries', 'description' => 'Keep ingredients, staff time, and closing routines under control.', 'accent' => 'emerald'],
        ];
    }

    /**
     * @return list<array{title: string, description: string, icon: string}>
     */
    public function whyChoose(): array
    {
        return [
            ['title' => 'One-Time Payment', 'description' => 'Pay £29 once. There are no monthly fees, yearly renewals, or hidden charges.', 'icon' => 'affordable'],
            ['title' => 'Built for Real Businesses', 'description' => 'Designed for restaurant and retail owners who need practical daily tools.', 'icon' => 'easy'],
            ['title' => 'Complete Operations Toolkit', 'description' => 'Cash up, attendance, inventory, suppliers, payroll prep, and reports in one license.', 'icon' => 'reports'],
            ['title' => 'Simple to Set Up', 'description' => 'Create your business, invite staff, and begin using core workflows without a long project.', 'icon' => 'fast'],
            ['title' => 'Role-Based Security', 'description' => 'Control who can see and change sensitive business information.', 'icon' => 'security'],
            ['title' => 'Clear Daily Reports', 'description' => 'Understand cash, staff, and inventory with reports that are easy to read.', 'icon' => 'analytics'],
            ['title' => 'Reliable Daily Use', 'description' => 'Built for busy trading hours when your team needs software that stays out of the way.', 'icon' => 'reliable'],
            ['title' => 'Free Updates in Version Cycle', 'description' => 'Receive updates during the current version cycle after you purchase your license.', 'icon' => 'cloud'],
        ];
    }

    /**
     * @return array{name: string, price: string, badge: string, description: string, cta: string, note: string, features: list<string>}
     */
    public function pricingPlan(): array
    {
        return [
            'name' => (string) config('totalcashpro.pricing.label', 'Lifetime License'),
            'price' => (string) config('totalcashpro.pricing.amount', '£29'),
            'badge' => (string) config('totalcashpro.pricing.badge', 'One-Time Payment'),
            'description' => 'Own professional restaurant and retail management software with a single payment.',
            'cta' => 'Buy Now',
            'note' => 'No subscriptions. No recurring charges.',
            'features' => [
                'Complete Cash Up Management',
                'Employee Clock In & Clock Out',
                'Attendance Tracking',
                'Staff Management',
                'Inventory Management',
                'Supplier Management',
                'Payroll Management',
                'Daily Reports',
                'Secure Role Management',
                'Free Updates During Version Cycle',
                'Responsive Dashboard',
                'Easy Setup',
                'Professional Support',
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
                'quote' => 'We needed something affordable that covered cash up and staff time. Paying once for TotalCashPro made more sense than another monthly bill.',
                'name' => 'Amelia Hart',
                'role' => 'Operations Manager',
                'business' => 'Northbridge Kitchen',
            ],
            [
                'quote' => 'Attendance, inventory, and daily reports are finally in one place. The price is clear, and the software does the jobs we actually need.',
                'name' => 'Daniel Okoye',
                'role' => 'Owner',
                'business' => 'Harbour Retail Group',
            ],
            [
                'quote' => 'Our managers can close the day without chasing spreadsheets. For £29, it was an easy decision for our cafe group.',
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
                'answer' => 'TotalCashPro is £29 as a one-time payment. There are no monthly fees, yearly fees, or hidden costs.',
            ],
            [
                'question' => 'Is this a subscription?',
                'answer' => 'No. Customers pay once and own the software license. There are no recurring charges.',
            ],
            [
                'question' => 'What is included in the Lifetime License?',
                'answer' => 'You get cash up, clock in/out, attendance, staff management, inventory, suppliers, payroll management, daily reports, role management, a responsive dashboard, easy setup, professional support, and free updates during the version cycle.',
            ],
            [
                'question' => 'Who is TotalCashPro for?',
                'answer' => 'It is built for restaurants, coffee shops, retail stores, pharmacies, salons, bakeries, convenience stores, food trucks, and similar businesses that need practical daily operations software.',
            ],
            [
                'question' => 'Do I need technical expertise to get started?',
                'answer' => 'No. The software is designed for business owners and managers. You can set up your business, invite staff, and begin core workflows without a complex technical project.',
            ],
            [
                'question' => 'Can I see the features before buying?',
                'answer' => 'Yes. Use View Features on the homepage to review the full toolkit, then purchase the Lifetime License when you are ready.',
            ],
        ];
    }
}
