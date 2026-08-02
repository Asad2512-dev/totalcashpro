<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailTemplateController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'New Email Template',
            'active' => 'email-templates',
            'action' => route('super-admin.email-templates.store'),
            'cancelRoute' => route('super-admin.email-templates'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->content->saveEmailTemplate($this->validated($request));

        return redirect()->route('super-admin.email-templates')->with('status', 'Template created.');
    }

    public function edit(EmailTemplate $email_template): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Email Template',
            'active' => 'email-templates',
            'action' => route('super-admin.email-templates.update', $email_template),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.email-templates'),
            'model' => $email_template,
            'fields' => $this->fields($email_template),
        ]);
    }

    public function update(Request $request, EmailTemplate $email_template): RedirectResponse
    {
        $this->content->saveEmailTemplate($this->validated($request), $email_template);

        return redirect()->route('super-admin.email-templates')->with('status', 'Template updated.');
    }

    public function destroy(EmailTemplate $email_template): RedirectResponse
    {
        $email_template->delete();

        return redirect()->route('super-admin.email-templates')->with('status', 'Template deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'trigger' => ['nullable', 'string', 'max:100'],
            'locale' => ['required', 'string', 'max:10'],
            'status' => ['required', 'in:draft,published,archived'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?EmailTemplate $template = null): array
    {
        return [
            ['name' => 'name', 'value' => $template?->name],
            ['name' => 'slug', 'value' => $template?->slug],
            ['name' => 'subject', 'value' => $template?->subject, 'full' => true],
            ['name' => 'trigger', 'value' => $template?->trigger],
            ['name' => 'locale', 'value' => $template?->locale ?? 'en'],
            ['name' => 'status', 'type' => 'select', 'value' => $template?->status->value ?? 'draft', 'options' => ['draft' => 'Draft', 'published' => 'Published']],
            ['name' => 'body', 'type' => 'textarea', 'value' => $template?->body, 'full' => true, 'rows' => 8],
        ];
    }
}
