<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class ApplicationLogo extends Component
{
    public function render(): View
    {
        return view('core::components.application-logo');
    }
}
