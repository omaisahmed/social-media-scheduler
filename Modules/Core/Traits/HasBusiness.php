<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Context;

/**
 * Scopes every model to the currently authenticated business.
 *
 * Modules that need to query across businesses (background workers,
 * console commands) can use `Model::withoutBusinessScope()` to run the
 * given callable with the global scope disabled.
 */
trait HasBusiness
{
    /**
     * Flag to bypass the business scope for a single callable run.
     */
    public static bool $withoutBusinessScope = false;

    /**
     * Whether the business scope applies to this model.
     *
     * The tenant root itself (Business) is exempt.
     */
    protected function hasBusinessScope(): bool
    {
        return true;
    }

    public static function bootHasBusiness(): void
    {
        static::addGlobalScope('business', function (Builder $builder) {
            if (static::$withoutBusinessScope) {
                return;
            }

            if (! (new static)->hasBusinessScope()) {
                return;
            }

            $businessId = Context::get('business_id') ?? auth()->user()?->business_id;

            if ($businessId !== null) {
                $builder->where($builder->getModel()->getTable().'.business_id', $businessId);
            }
        });
    }

    /**
     * Run a callback with the business global scope disabled.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function withoutBusinessScope(callable $callback)
    {
        static::$withoutBusinessScope = true;

        try {
            return $callback();
        } finally {
            static::$withoutBusinessScope = false;
        }
    }
}
