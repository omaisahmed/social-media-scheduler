<?php

declare(strict_types=1);

namespace Modules\Templates\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Templates\Repositories\Contracts\PostTemplateRepositoryInterface;

final class TemplateController
{
    public function __construct(protected PostTemplateRepositoryInterface $templates)
    {
    }

    public function index(Request $request): View
    {
        return view('templates::index', [
            'templates' => $this->templates->paginateForBusiness(
                $request->user()->business_id,
                $request->only(['search']),
            ),
        ]);
    }

    public function create(): View
    {
        return view('templates::create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $this->templates->create([
            'business_id' => $request->user()->business_id,
            'user_id' => $request->user()->getKey(),
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'tags' => $this->parseTags($validated['tags'] ?? null),
        ]);

        return redirect()->route('templates.index')->with('status', 'template-created');
    }

    public function edit(Request $request, int $templateId): View
    {
        $template = $this->templates->findForBusiness($request->user()->business_id, $templateId);

        abort_unless($template, 404);

        return view('templates::edit', ['template' => $template]);
    }

    public function update(Request $request, int $templateId): RedirectResponse
    {
        $template = $this->templates->findForBusiness($request->user()->business_id, $templateId);

        abort_unless($template, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $this->templates->update($templateId, [
            'name' => $validated['name'],
            'content' => $validated['content'] ?? null,
            'tags' => $this->parseTags($validated['tags'] ?? null),
        ]);

        return redirect()->route('templates.edit', $templateId)->with('status', 'template-updated');
    }

    public function destroy(Request $request, int $templateId): RedirectResponse
    {
        $template = $this->templates->findForBusiness($request->user()->business_id, $templateId);

        abort_unless($template, 404);

        $this->templates->delete($templateId);

        return redirect()->route('templates.index')->with('status', 'template-deleted');
    }

    /**
     * @return array<int, string>
     */
    protected function parseTags(?string $tags): array
    {
        if (! $tags) {
            return [];
        }

        return collect(preg_split('/[,#]/', $tags) ?: [])
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }
}
