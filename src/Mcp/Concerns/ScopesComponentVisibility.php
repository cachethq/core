<?php

namespace Cachet\Mcp\Concerns;

use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Models\Component;
use Illuminate\Database\Eloquent\Builder;

trait ScopesComponentVisibility
{
    use ChecksApiAuthentication;

    /**
     * Base query scoping components to those visible to the current caller.
     *
     * Components have no visibility of their own; they inherit it from their
     * group. Ungrouped components are always public and disabled components
     * are hidden from guests, matching the status page and the REST API.
     *
     * @return Builder<Component>
     */
    protected function visibleComponents(): Builder
    {
        return Component::query()
            ->with(['unresolvedIncidents', 'activeMaintenance'])
            ->visibleTo($this->isAuthenticated());
    }
}
