<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use App\Models\CmsFeature;
use App\Models\CmsHeroSection;
use App\Models\CmsPage;
use App\Models\CmsTestimonial;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class CmsController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function createPage(): View
    {
        return $this->form('CMS Page', 'pages', route('super-admin.cms.pages.store'), [
            ['name' => 'title'], ['name' => 'slug'],
            ['name' => 'content', 'type' => 'textarea', 'full' => true, 'rows' => 8],
            ['name' => 'status', 'type' => 'select', 'value' => 'draft', 'options' => ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']],
        ]);
    }

    public function storePage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }
        $this->content->saveCmsPage($data);

        return redirect()->route('super-admin.cms.pages')->with('status', 'CMS page saved.');
    }

    public function editPage(CmsPage $page): View
    {
        return $this->form('Edit CMS Page', 'pages', route('super-admin.cms.pages.update', $page), [
            ['name' => 'title', 'value' => $page->title],
            ['name' => 'slug', 'value' => $page->slug],
            ['name' => 'content', 'type' => 'textarea', 'value' => $page->content, 'full' => true, 'rows' => 8],
            ['name' => 'status', 'type' => 'select', 'value' => $page->status->value, 'options' => ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']],
        ], 'PUT', $page);
    }

    public function updatePage(Request $request, CmsPage $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
        $this->content->saveCmsPage($data, $page);

        return redirect()->route('super-admin.cms.pages')->with('status', 'CMS page updated.');
    }

    public function destroyPage(CmsPage $page): RedirectResponse
    {
        $this->content->deleteCmsPage($page);

        return redirect()->route('super-admin.cms.pages')->with('status', 'CMS page deleted.');
    }

    public function createHero(): View
    {
        return $this->form('Hero Section', 'hero', route('super-admin.cms.hero.store'), $this->heroFields());
    }

    public function storeHero(Request $request): RedirectResponse
    {
        $this->content->saveHero($this->validateHero($request));

        return redirect()->route('super-admin.cms.hero')->with('status', 'Hero saved.');
    }

    public function editHero(CmsHeroSection $hero): View
    {
        return $this->form('Edit Hero', 'hero', route('super-admin.cms.hero.update', $hero), $this->heroFields($hero), 'PUT', $hero);
    }

    public function updateHero(Request $request, CmsHeroSection $hero): RedirectResponse
    {
        $this->content->saveHero($this->validateHero($request), $hero);

        return redirect()->route('super-admin.cms.hero')->with('status', 'Hero updated.');
    }

    public function createFeature(): View
    {
        return $this->form('Feature', 'features', route('super-admin.cms.features.store'), $this->featureFields());
    }

    public function storeFeature(Request $request): RedirectResponse
    {
        $this->content->saveFeature($this->validateFeature($request));

        return redirect()->route('super-admin.cms.features')->with('status', 'Feature saved.');
    }

    public function editFeature(CmsFeature $feature): View
    {
        return $this->form('Edit Feature', 'features', route('super-admin.cms.features.update', $feature), $this->featureFields($feature), 'PUT', $feature);
    }

    public function updateFeature(Request $request, CmsFeature $feature): RedirectResponse
    {
        $this->content->saveFeature($this->validateFeature($request), $feature);

        return redirect()->route('super-admin.cms.features')->with('status', 'Feature updated.');
    }

    public function createTestimonial(): View
    {
        return $this->form('Testimonial', 'testimonials', route('super-admin.cms.testimonials.store'), $this->testimonialFields());
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $data = $this->validateTestimonial($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $this->content->saveTestimonial($data);

        return redirect()->route('super-admin.cms.testimonials')->with('status', 'Testimonial saved.');
    }

    public function editTestimonial(CmsTestimonial $testimonial): View
    {
        return $this->form('Edit Testimonial', 'testimonials', route('super-admin.cms.testimonials.update', $testimonial), $this->testimonialFields($testimonial), 'PUT', $testimonial);
    }

    public function updateTestimonial(Request $request, CmsTestimonial $testimonial): RedirectResponse
    {
        $data = $this->validateTestimonial($request);
        $data['is_featured'] = $request->boolean('is_featured');
        $this->content->saveTestimonial($data, $testimonial);

        return redirect()->route('super-admin.cms.testimonials')->with('status', 'Testimonial updated.');
    }

    public function createFaq(): View
    {
        return $this->form('FAQ', 'faq', route('super-admin.cms.faq.store'), $this->faqFields());
    }

    public function storeFaq(Request $request): RedirectResponse
    {
        $this->content->saveFaq($this->validateFaq($request));

        return redirect()->route('super-admin.cms.faq')->with('status', 'FAQ saved.');
    }

    public function editFaq(CmsFaq $faq): View
    {
        return $this->form('Edit FAQ', 'faq', route('super-admin.cms.faq.update', $faq), $this->faqFields($faq), 'PUT', $faq);
    }

    public function updateFaq(Request $request, CmsFaq $faq): RedirectResponse
    {
        $this->content->saveFaq($this->validateFaq($request), $faq);

        return redirect()->route('super-admin.cms.faq')->with('status', 'FAQ updated.');
    }

    public function destroyHero(CmsHeroSection $hero): RedirectResponse
    {
        $this->content->deleteHero($hero);

        return redirect()->route('super-admin.cms.hero')->with('status', 'Hero deleted.');
    }

    public function destroyFeature(CmsFeature $feature): RedirectResponse
    {
        $this->content->deleteFeature($feature);

        return redirect()->route('super-admin.cms.features')->with('status', 'Feature deleted.');
    }

    public function destroyTestimonial(CmsTestimonial $testimonial): RedirectResponse
    {
        $this->content->deleteTestimonial($testimonial);

        return redirect()->route('super-admin.cms.testimonials')->with('status', 'Testimonial deleted.');
    }

    public function destroyFaq(CmsFaq $faq): RedirectResponse
    {
        $this->content->deleteFaq($faq);

        return redirect()->route('super-admin.cms.faq')->with('status', 'FAQ deleted.');
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     */
    private function form(string $title, string $active, string $action, array $fields, string $method = 'POST', mixed $model = null): View
    {
        return view('admin.crud.form', [
            'title' => $title,
            'active' => $active,
            'action' => $action,
            'method' => $method,
            'cancelRoute' => route('super-admin.cms.'.$active),
            'fields' => $fields,
            'model' => $model,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateHero(Request $request): array
    {
        return $request->validate([
            'page_key' => ['required', 'string', 'max:50'],
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'headline' => ['required', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string'],
            'primary_cta_label' => ['nullable', 'string', 'max:100'],
            'primary_cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta_label' => ['nullable', 'string', 'max:100'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function heroFields(?CmsHeroSection $hero = null): array
    {
        return [
            ['name' => 'page_key', 'value' => $hero?->page_key ?? 'home'],
            ['name' => 'eyebrow', 'value' => $hero?->eyebrow],
            ['name' => 'headline', 'value' => $hero?->headline, 'full' => true],
            ['name' => 'subheadline', 'type' => 'textarea', 'value' => $hero?->subheadline, 'full' => true],
            ['name' => 'primary_cta_label', 'value' => $hero?->primary_cta_label],
            ['name' => 'primary_cta_url', 'value' => $hero?->primary_cta_url],
            ['name' => 'secondary_cta_label', 'value' => $hero?->secondary_cta_label],
            ['name' => 'secondary_cta_url', 'value' => $hero?->secondary_cta_url],
            ['name' => 'sort_order', 'type' => 'number', 'value' => $hero?->sort_order ?? 0],
            ['name' => 'status', 'type' => 'select', 'value' => $hero?->status->value ?? 'published', 'options' => ['draft' => 'Draft', 'published' => 'Published']],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateFeature(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'plan_slug' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function featureFields(?CmsFeature $feature = null): array
    {
        return [
            ['name' => 'title', 'value' => $feature?->title],
            ['name' => 'plan_slug', 'value' => $feature?->plan_slug],
            ['name' => 'sort_order', 'type' => 'number', 'value' => $feature?->sort_order ?? 0],
            ['name' => 'status', 'type' => 'select', 'value' => $feature?->status->value ?? 'published', 'options' => ['draft' => 'Draft', 'published' => 'Published']],
            ['name' => 'description', 'type' => 'textarea', 'value' => $feature?->description, 'full' => true],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTestimonial(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'business' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function testimonialFields(?CmsTestimonial $item = null): array
    {
        return [
            ['name' => 'name', 'value' => $item?->name],
            ['name' => 'role', 'value' => $item?->role],
            ['name' => 'business', 'value' => $item?->business],
            ['name' => 'quote', 'type' => 'textarea', 'value' => $item?->quote, 'full' => true],
            ['name' => 'sort_order', 'type' => 'number', 'value' => $item?->sort_order ?? 0],
            ['name' => 'status', 'type' => 'select', 'value' => $item?->status->value ?? 'published', 'options' => ['draft' => 'Draft', 'published' => 'Published']],
            ['name' => 'is_featured', 'type' => 'checkbox', 'label' => 'Featured', 'value' => $item?->is_featured],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateFaq(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function faqFields(?CmsFaq $faq = null): array
    {
        return [
            ['name' => 'question', 'value' => $faq?->question, 'full' => true],
            ['name' => 'answer', 'type' => 'textarea', 'value' => $faq?->answer, 'full' => true],
            ['name' => 'sort_order', 'type' => 'number', 'value' => $faq?->sort_order ?? 0],
            ['name' => 'status', 'type' => 'select', 'value' => $faq?->status->value ?? 'published', 'options' => ['draft' => 'Draft', 'published' => 'Published']],
        ];
    }
}
