<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\CmsFaq;
use App\Models\CmsFeature;
use App\Models\CmsHeroSection;
use App\Models\CmsPage;
use App\Models\CmsTestimonial;
use App\Models\EmailTemplate;
use App\Models\MediaAsset;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ContentManagementService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveCmsPage(array $data, ?CmsPage $page = null): CmsPage
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);

        if ($page) {
            $old = $page->toArray();
            $page->update($data);
            $this->logAdminAction('cms.page_updated', 'CMS page updated: '.$page->title, $page, $old, $page->fresh()?->toArray());

            return $page->refresh();
        }

        $page = CmsPage::query()->create($data);
        $this->logAdminAction('cms.page_created', 'CMS page created: '.$page->title, $page);

        return $page;
    }

    public function deleteCmsPage(CmsPage $page): void
    {
        $snapshot = $page->toArray();
        $title = $page->title;
        $page->delete();
        $this->logAdminAction('cms.page_deleted', 'CMS page deleted: '.$title, null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveHero(array $data, ?CmsHeroSection $hero = null): CmsHeroSection
    {
        if ($hero) {
            $hero->update($data);
            $this->logAdminAction('cms.hero_updated', 'Hero section updated', $hero);

            return $hero->refresh();
        }

        $hero = CmsHeroSection::query()->create($data);
        $this->logAdminAction('cms.hero_created', 'Hero section created', $hero);

        return $hero;
    }

    public function deleteHero(CmsHeroSection $hero): void
    {
        $snapshot = $hero->toArray();
        $hero->delete();
        $this->logAdminAction('cms.hero_deleted', 'Hero section deleted', null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveFeature(array $data, ?CmsFeature $feature = null): CmsFeature
    {
        if ($feature) {
            $feature->update($data);
            $this->logAdminAction('cms.feature_updated', 'Feature updated: '.$feature->title, $feature);

            return $feature->refresh();
        }

        $feature = CmsFeature::query()->create($data);
        $this->logAdminAction('cms.feature_created', 'Feature created: '.$feature->title, $feature);

        return $feature;
    }

    public function deleteFeature(CmsFeature $feature): void
    {
        $snapshot = $feature->toArray();
        $title = $feature->title;
        $feature->delete();
        $this->logAdminAction('cms.feature_deleted', 'Feature deleted: '.$title, null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveTestimonial(array $data, ?CmsTestimonial $item = null): CmsTestimonial
    {
        if ($item) {
            $item->update($data);
            $this->logAdminAction('cms.testimonial_updated', 'Testimonial updated', $item);

            return $item->refresh();
        }

        $item = CmsTestimonial::query()->create($data);
        $this->logAdminAction('cms.testimonial_created', 'Testimonial created', $item);

        return $item;
    }

    public function deleteTestimonial(CmsTestimonial $item): void
    {
        $snapshot = $item->toArray();
        $item->delete();
        $this->logAdminAction('cms.testimonial_deleted', 'Testimonial deleted', null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveFaq(array $data, ?CmsFaq $faq = null): CmsFaq
    {
        if ($faq) {
            $faq->update($data);
            $this->logAdminAction('cms.faq_updated', 'FAQ updated', $faq);

            return $faq->refresh();
        }

        $faq = CmsFaq::query()->create($data);
        $this->logAdminAction('cms.faq_created', 'FAQ created', $faq);

        return $faq;
    }

    public function deleteFaq(CmsFaq $faq): void
    {
        $snapshot = $faq->toArray();
        $faq->delete();
        $this->logAdminAction('cms.faq_deleted', 'FAQ deleted', null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveAnnouncement(array $data, ?Announcement $announcement = null): Announcement
    {
        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($announcement) {
            $announcement->update($data);
            $this->logAdminAction('announcement.updated', 'Announcement updated: '.$announcement->title, $announcement);

            return $announcement->refresh();
        }

        $announcement = Announcement::query()->create($data);
        $this->logAdminAction('announcement.created', 'Announcement created: '.$announcement->title, $announcement);

        return $announcement;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveEmailTemplate(array $data, ?EmailTemplate $template = null): EmailTemplate
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);

        if ($template) {
            $template->update($data);
            $this->logAdminAction('email_template.updated', 'Email template updated: '.$template->name, $template);

            return $template->refresh();
        }

        $template = EmailTemplate::query()->create($data);
        $this->logAdminAction('email_template.created', 'Email template created: '.$template->name, $template);

        return $template;
    }

    public function uploadMedia(UploadedFile $file, ?string $folder = null, ?string $collection = null): MediaAsset
    {
        $path = $file->store($folder ? 'media/'.$folder : 'media', 'public');

        $asset = MediaAsset::query()->create([
            'disk' => 'public',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'collection' => $collection,
            'folder' => $folder,
            'uploaded_by' => auth()->id(),
        ]);

        $this->logAdminAction('media.uploaded', 'Media uploaded: '.$asset->filename, $asset);

        return $asset;
    }

    public function deleteMedia(MediaAsset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);
        $snapshot = $asset->toArray();
        $name = $asset->filename;
        $asset->delete();
        $this->logAdminAction('media.deleted', 'Media deleted: '.$name, null, $snapshot);
    }

    /**
     * @param  array<string, array<string, string|null>>  $grouped
     */
    public function saveSettings(array $grouped): void
    {
        foreach ($grouped as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                Setting::query()->updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value, 'type' => 'string'],
                );
            }
        }

        $this->logAdminAction('settings.updated', 'Platform settings updated', null, null, ['groups' => array_keys($grouped)]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createNotification(array $data): AppNotification
    {
        $notification = AppNotification::query()->create($data);
        $this->logAdminAction('notification.created', 'Notification created: '.$notification->title, $notification);

        return $notification;
    }

    public function markNotificationRead(AppNotification $notification): AppNotification
    {
        $notification->update(['read_at' => now()]);

        return $notification->refresh();
    }

    public function archiveNotification(AppNotification $notification): AppNotification
    {
        $notification->update(['archived_at' => now(), 'read_at' => $notification->read_at ?? now()]);

        return $notification->refresh();
    }

    public function deleteNotification(AppNotification $notification): void
    {
        $notification->delete();
        $this->logAdminAction('notification.deleted', 'Notification deleted');
    }
}
