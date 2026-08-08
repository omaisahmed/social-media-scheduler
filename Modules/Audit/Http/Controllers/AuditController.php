<?php

declare(strict_types=1);

namespace Modules\Audit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Audit\Models\AuditLog;

final class AuditController
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->where('business_id', $request->user()->business_id)
            ->with('user')
            ->latest();

        if ($event = $request->query('event')) {
            $query->where('event', $event);
        }

        if ($auditableType = $request->query('auditable_type')) {
            $query->where('auditable_type', $auditableType);
        }

        return view('audit::index', [
            'logs' => $query->paginate(25),
            'events' => $this->availableEvents($request->user()->business_id),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function availableEvents(int $businessId): array
    {
        return AuditLog::query()
            ->where('business_id', $businessId)
            ->distinct()
            ->orderBy('event')
            ->pluck('event')
            ->all();
    }
}
