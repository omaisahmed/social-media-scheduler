<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\View\View;
use Modules\Business\Models\Business;
use Modules\Settings\Models\Setting;

final class SettingsController
{
    public function index(Request $request): View
    {
        $business = Business::withoutBusinessScope(fn () => Business::find($request->user()->business_id));

        return view('settings::index', [
            'business' => $business,
            'user' => $request->user(),
            'preferences' => $this->preferences($request->user()),
        ]);
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $business = Business::withoutBusinessScope(fn () => Business::find($request->user()->business_id));

        abort_unless($business, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'timezone'],
            'default_platform' => ['nullable', 'string', 'max:60'],
        ]);

        $business->update([
            'name' => $validated['name'],
            'primary_timezone' => $validated['timezone'],
        ]);

        Setting::setFor($business, 'default_platform', $validated['default_platform'] ?? '');

        return back()->with('status', 'business-updated');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notify_post_published' => ['nullable', 'boolean'],
            'notify_post_failed' => ['nullable', 'boolean'],
            'notify_daily_summary' => ['nullable', 'boolean'],
        ]);

        $preferences = [
            'notify_post_published' => $request->boolean('notify_post_published'),
            'notify_post_failed' => $request->boolean('notify_post_failed'),
            'notify_daily_summary' => $request->boolean('notify_daily_summary'),
        ];

        foreach ($preferences as $key => $value) {
            Setting::setFor($request->user(), $key, $value);
        }

        return back()->with('status', 'notifications-updated');
    }

    /**
     * @return array<string, bool>
     */
    protected function preferences($user): array
    {
        return [
            'notify_post_published' => (bool) Setting::getFor($user, 'notify_post_published', true),
            'notify_post_failed' => (bool) Setting::getFor($user, 'notify_post_failed', true),
            'notify_daily_summary' => (bool) Setting::getFor($user, 'notify_daily_summary', false),
        ];
    }
}
