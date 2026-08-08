<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Badge extends Component
{
    public function __construct(
        public string $color = 'gray',
    ) {
    }

    public function render(): View
    {
        return view('core::components.badge');
    }
}
