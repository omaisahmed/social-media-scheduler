<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Context;
use Modules\Audit\Actions\RecordAuditAction;

/**
 * Automatically records an audit trail entry when a model is created,
 * updated or deleted.
 *
 * Modules can opt out per-class by defining a static
 * `public array $auditIgnore = [...]` or setting `$auditEnabled = false`.
 */
trait HasAudit
{
    /**
     * Whether auditing is enabled for this model.
     */
    public static bool $auditEnabled = true;

    /**
     * Attributes that should never be recorded in the audit trail.
     */
    public static array $auditHiddenAttributes = ['password', 'remember_token', 'access_token', 'refresh_token'];

    public static function bootHasAudit(): void
    {
        static::saved(function ($model) {
            $model->handleAudit('saved');
        });

        static::deleted(function ($model) {
            $model->handleAudit('deleted');
        });
    }

    protected function handleAudit(string $event): void
    {
        if (! static::$auditEnabled || ! class_exists(RecordAuditAction::class)) {
            return;
        }

        $dirty = $this->getDirty();
        $old = [];
        $new = [];

        foreach ($dirty as $key => $value) {
            if (in_array($key, static::$auditHiddenAttributes, true)) {
                continue;
            }

            $old[$key] = $this->getOriginal($key);
            $new[$key] = $value;
        }

        if ($event === 'deleted') {
            $new = $this->getAttributes();
        }

        $businessId = Context::get('business_id')
            ?? $this->business_id
            ?? (method_exists($this, 'business') ? $this->business?->getKey() : null);

        app(RecordAuditAction::class)->execute(
            action: $event,
            entityType: static::class,
            entityId: $this->getKey(),
            oldValues: $event === 'saved' ? $old : ($this->getOriginal() ?: null),
            newValues: $event === 'saved' ? $new : $this->getAttributes(),
            businessId: $businessId,
        );
    }
}
