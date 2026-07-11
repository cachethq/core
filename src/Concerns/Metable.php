<?php

namespace Cachet\Concerns;

interface Metable
{
    /**
     * Get the metadata as a key/value array.
     *
     * @return array<string, mixed>
     */
    public function metaValues(): array;

    /**
     * Sync the given key/value pairs into the model's metadata.
     *
     * @param  array<string, mixed>  $meta
     */
    public function syncMeta(array $meta): void;
}
