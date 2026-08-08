<?php

declare(strict_types=1);

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'model_type',
        'model_id',
        'key',
        'value',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get a value for a given model + key, or the default.
     */
    public static function getFor(Model $model, string $key, mixed $default = null): mixed
    {
        $setting = static::query()
            ->where('model_type', $model->getMorphClass())
            ->where('model_id', $model->getKey())
            ->where('key', $key)
            ->first();

        return $setting?->value ?? $default;
    }

    /**
     * Set a value for a given model + key.
     */
    public static function setFor(Model $model, string $key, mixed $value): Setting
    {
        $encoded = is_string($value) ? $value : json_encode($value);

        return static::query()->updateOrCreate(
            [
                'model_type' => $model->getMorphClass(),
                'model_id' => $model->getKey(),
                'key' => $key,
            ],
            ['value' => $encoded],
        );
    }
}
