<?php

namespace Cachet\Support\Settings;

use Cachet\Support\Settings\Drivers\CacheDriver;
use Cachet\Support\Settings\Drivers\EloquentDriver;
use Cachet\Support\Settings\Drivers\FileDriver;
use Illuminate\Cache\Repository;
use Illuminate\Support\Manager;

class SettingManager extends Manager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('cachet.settings.default', 'eloquent');
    }

    /**
     * Create the eloquent driver.
     */
    public function createEloquentDriver()
    {
        return new EloquentDriver();
    }

    /**
     * Create the cache driver.
     */
    public function createCacheDriver()
    {
        return new CacheDriver(
            app(Repository::class),
            $this->createEloquentDriver(),
        );
    }

    /**
     * Create the file driver.
     */
    public function createFileDriver()
    {
        return new FileDriver(
            $this->createEloquentDriver()
        );
    }
}
