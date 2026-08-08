<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

final class NotificationsDropdown extends Component
{
    public function render(): View
    {
        $user = Auth::user();

        return view('core::components.notifications-dropdown', [
            'unreadCount' => $user ? $user->unreadNotifications()->count() : 0,
            'notifications' => $user
                ? $user->notifications()->take(10)->get()
                : collect(),
            'read' => $user
                ? $user->notifications()->whereNotNull('read_at')->take(5)->get()
                : collect(),
        ]);
    }
}
