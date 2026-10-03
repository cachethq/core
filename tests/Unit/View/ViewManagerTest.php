<?php

use Cachet\View\ViewManager;

it('renders global and matching scoped hooks once', function () {
    $viewManager = new ViewManager;
    $sharedHook = fn () => 'global';

    $viewManager->registerRenderHook('status', $sharedHook);
    $viewManager->registerRenderHook('status', $sharedHook, 'public');
    $viewManager->registerRenderHook('status', fn () => 'public', 'public');
    $viewManager->registerRenderHook('status', fn () => 'dashboard', 'dashboard');

    expect($viewManager->renderHook('status', 'public')->toHtml())
        ->toBe('globalpublic');
});
