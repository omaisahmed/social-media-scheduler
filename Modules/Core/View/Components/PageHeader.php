<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class PageHeader extends Component
{
    public function __construct(
        public string $title,
        public ?string $description = null,
    ) {
    }

    public function render(): View
    {
        return view('core::components.page-header');
    }
}
