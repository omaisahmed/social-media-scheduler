<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public bool $padding = true,
    ) {
    }

    public function render(): View
    {
        return view('core::components.card');
    }
}
