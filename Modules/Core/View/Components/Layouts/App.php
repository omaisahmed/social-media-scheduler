<?php

declare(strict_types=1);

namespace Modules\Core\View\Components\Layouts;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class App extends Component
{
    public function render(): View
    {
        return view('core::layouts.app');
    }
}
