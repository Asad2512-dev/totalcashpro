<?php

declare(strict_types=1);

use App\Http\Controllers\SuperAdmin\AccessRequestController;
use App\Http\Controllers\SuperAdmin\AnnouncementController;
use App\Http\Controllers\SuperAdmin\BranchController;
use App\Http\Controllers\SuperAdmin\CmsController;
use App\Http\Controllers\SuperAdmin\ContactMessageController;
use App\Http\Controllers\SuperAdmin\CouponController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\DiscountController;
use App\Http\Controllers\SuperAdmin\EmailTemplateController;
use App\Http\Controllers\SuperAdmin\ExportController;
use App\Http\Controllers\SuperAdmin\MediaController;
use App\Http\Controllers\SuperAdmin\NotificationController;
use App\Http\Controllers\SuperAdmin\OrganizationController;
use App\Http\Controllers\SuperAdmin\PageController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\PermissionController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\SearchController;
use App\Http\Controllers\SuperAdmin\SettingController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\SupportTicketController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/search', SearchController::class)->name('search');
Route::get('/export', ExportController::class)->name('export');

$pages = [
    'businesses',
    'organizations',
    'business-requests',
    'branches',
    'users',
    'plans',
    'subscriptions',
    'coupons',
    'discounts',
    'trials',
    'payments',
    'revenue',
    'analytics',
    'support',
    'announcements',
    'notifications',
    'email-templates',
    'media',
    'contact-messages',
    'audit-logs',
    'activity',
    'system-health',
    'settings',
    'roles',
    'permissions',
    'profile',
];

foreach ($pages as $page) {
    Route::get('/'.$page, PageController::class)->name($page);
}

Route::prefix('cms')->name('cms.')->group(function (): void {
    foreach (['pages', 'hero', 'features', 'pricing', 'testimonials', 'faq', 'contact', 'footer'] as $cmsPage) {
        Route::get('/'.$cmsPage, PageController::class)->name($cmsPage);
    }

    Route::get('/pages/create', [CmsController::class, 'createPage'])->name('pages.create');
    Route::post('/pages', [CmsController::class, 'storePage'])->name('pages.store');
    Route::get('/pages/{page}/edit', [CmsController::class, 'editPage'])->name('pages.edit');
    Route::put('/pages/{page}', [CmsController::class, 'updatePage'])->name('pages.update');
    Route::delete('/pages/{page}', [CmsController::class, 'destroyPage'])->name('pages.destroy');

    Route::get('/hero/create', [CmsController::class, 'createHero'])->name('hero.create');
    Route::post('/hero', [CmsController::class, 'storeHero'])->name('hero.store');
    Route::get('/hero/{hero}/edit', [CmsController::class, 'editHero'])->name('hero.edit');
    Route::put('/hero/{hero}', [CmsController::class, 'updateHero'])->name('hero.update');

    Route::get('/features/create', [CmsController::class, 'createFeature'])->name('features.create');
    Route::post('/features', [CmsController::class, 'storeFeature'])->name('features.store');
    Route::get('/features/{feature}/edit', [CmsController::class, 'editFeature'])->name('features.edit');
    Route::put('/features/{feature}', [CmsController::class, 'updateFeature'])->name('features.update');

    Route::get('/testimonials/create', [CmsController::class, 'createTestimonial'])->name('testimonials.create');
    Route::post('/testimonials', [CmsController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::get('/testimonials/{testimonial}/edit', [CmsController::class, 'editTestimonial'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [CmsController::class, 'updateTestimonial'])->name('testimonials.update');

    Route::get('/faq/create', [CmsController::class, 'createFaq'])->name('faq.create');
    Route::post('/faq', [CmsController::class, 'storeFaq'])->name('faq.store');
    Route::get('/faq/{faq}/edit', [CmsController::class, 'editFaq'])->name('faq.edit');
    Route::put('/faq/{faq}', [CmsController::class, 'updateFaq'])->name('faq.update');
    Route::delete('/faq/{faq}', [CmsController::class, 'destroyFaq'])->name('faq.destroy');

    Route::delete('/hero/{hero}', [CmsController::class, 'destroyHero'])->name('hero.destroy');
    Route::delete('/features/{feature}', [CmsController::class, 'destroyFeature'])->name('features.destroy');
    Route::delete('/testimonials/{testimonial}', [CmsController::class, 'destroyTestimonial'])->name('testimonials.destroy');
});

Route::post('organizations/bulk', [OrganizationController::class, 'bulk'])->name('organizations.bulk');
Route::resource('organizations', OrganizationController::class)->except(['index']);
Route::post('organizations/{organization}/suspend', [OrganizationController::class, 'suspend'])->name('organizations.suspend');
Route::post('organizations/{organization}/activate', [OrganizationController::class, 'activate'])->name('organizations.activate');
Route::post('organizations/{organization}/send-credentials', [OrganizationController::class, 'sendCredentials'])->name('organizations.send-credentials');
Route::post('organizations/{organization}/login-as', [OrganizationController::class, 'loginAs'])->name('organizations.login-as');

Route::get('business-requests/{accessRequest}', [AccessRequestController::class, 'show'])->name('business-requests.show');
Route::post('business-requests/{accessRequest}/approve', [AccessRequestController::class, 'approve'])->name('business-requests.approve');
Route::post('business-requests/{accessRequest}/reject', [AccessRequestController::class, 'reject'])->name('business-requests.reject');
Route::post('business-requests/{accessRequest}/email', [AccessRequestController::class, 'email'])->name('business-requests.email');

Route::resource('branches', BranchController::class)->except(['index', 'show']);
Route::resource('users', UserController::class)->except(['index']);
Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

Route::resource('plans', PlanController::class)->except(['index', 'show']);
Route::post('plans/{plan}/enable', [PlanController::class, 'enable'])->name('plans.enable');
Route::post('plans/{plan}/disable', [PlanController::class, 'disable'])->name('plans.disable');

Route::resource('subscriptions', SubscriptionController::class)->only(['create', 'store', 'show']);
Route::post('subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
Route::post('subscriptions/{subscription}/activate', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');
Route::post('subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
Route::post('subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

Route::resource('coupons', CouponController::class)->except(['index', 'show']);
Route::resource('discounts', DiscountController::class)->except(['index', 'show']);

Route::resource('payments', PaymentController::class)->only(['create', 'store', 'show']);
Route::post('payments/{payment}/status', [PaymentController::class, 'markStatus'])->name('payments.status');

Route::get('support/create', [SupportTicketController::class, 'create'])->name('support.create');
Route::post('support', [SupportTicketController::class, 'store'])->name('support.store');
Route::get('support/{support}', [SupportTicketController::class, 'show'])->name('support.show');
Route::put('support/{support}', [SupportTicketController::class, 'update'])->name('support.update');
Route::post('support/{support}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
Route::post('support/{support}/close', [SupportTicketController::class, 'close'])->name('support.close');

Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);
Route::resource('notifications', NotificationController::class)->only(['create', 'store', 'destroy']);
Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
Route::post('notifications/{notification}/archive', [NotificationController::class, 'archive'])->name('notifications.archive');

Route::resource('email-templates', EmailTemplateController::class)->except(['index', 'show']);
Route::resource('media', MediaController::class)->only(['create', 'store', 'destroy']);

Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

Route::resource('roles', RoleController::class)->except(['index', 'show']);
Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
