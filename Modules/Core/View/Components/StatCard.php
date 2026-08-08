<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class StatCard extends Component
{
    public function __construct(
        public string $label,
        public string|int|float $value,
        public ?string $icon = null,
        public ?string $trend = null,
        public bool $trendUp = true,
        public string $color = 'indigo',
    ) {
    }

    public function render(): View
    {
        return view('core::components.stat-card');
    }
}
