<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Alert extends Component
{
    public function __construct(
        public string $type = 'info',
        public ?string $message = null,
        public bool $dismissible = false,
    ) {
    }

    public function render(): View
    {
        return view('core::components.alert');
    }
}
