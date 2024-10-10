<?php

namespace Cachet\View\Components;

use Closure;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Badge extends Component
{
    public function __construct(public readonly HasLabel&HasColor&HasIcon $status)
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('cachet::components.badge', [
            'color' => $this->status->getColor(),
            'label' => $this->status->getLabel(),
            'icon' => $this->status->getIcon(),
        ]);
    }
}
