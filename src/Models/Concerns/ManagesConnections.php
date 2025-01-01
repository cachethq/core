<?php

namespace Cachet\Models\Concerns;

trait ManagesConnections
{
    public function getConnectionName(): string
    {
        return config('cachet.database_connection');
    }
}