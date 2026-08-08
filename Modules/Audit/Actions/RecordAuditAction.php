<?php

declare(strict_types=1);

namespace Modules\Audit\Actions;

use Modules\Audit\Models\AuditLog;

final class RecordAuditAction
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function execute(
        string $action,
        string $entityType,
        int|string|null $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $businessId = null,
    ): ?AuditLog {
        $userId = null;
        $ip = null;
        $userAgent = null;

        try {
            $userId = auth()->id();
            $ip = request()->ip();
            $userAgent = substr((string) request()->userAgent(), 0, 255);
        } catch (\Throwable) {
            // Console / queue context has no HTTP request or authenticated user.
        }

        return AuditLog::create([
            'business_id' => $businessId,
            'user_id' => $userId,
            'event' => $action,
            'auditable_type' => $entityType,
            'auditable_id' => $entityId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'before' => $oldValues,
            'after' => $newValues,
        ]);
    }
}
