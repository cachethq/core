<?php

namespace Cachet\View\Components;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class About extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private AppSettings $settings)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.about', [
            'title' => $this->settings->name ?: config('cachet.title', 'Cachet'),
            'about' => Str::of($this->settings->about)->trim()->markdown()->toString(),
        ]);
    }
}
