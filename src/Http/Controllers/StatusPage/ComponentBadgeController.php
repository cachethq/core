<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Badger\Badger;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Http\BadgeResponseFactory;
use Cachet\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComponentBadgeController
{
    /**
     * Render a badge for a publicly visible component.
     */
    public function __invoke(Request $request, Component $component, Badger $badger, BadgeResponseFactory $badgeResponseFactory): Response
    {
        $component->loadMissing('group');

        abort_if(! $component->enabled || ($component->group?->visible !== null && $component->group->visible !== ResourceVisibilityEnum::guest), 404);

        return $badgeResponseFactory->make(
            $badger->generate(
                $component->name,
                $component->latest_status->getLabel(),
                $component->latest_status->getBadgeColor(),
                $badgeResponseFactory->style($request),
            ),
        );
    }
}
