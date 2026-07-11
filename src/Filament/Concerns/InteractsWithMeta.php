<?php

namespace Cachet\Filament\Concerns;

use Cachet\Concerns\Metable;
use Illuminate\Database\Eloquent\Model;

/**
 * Persists a `meta` KeyValue form field into the model's polymorphic metadata.
 *
 * @property array<string, mixed> $data
 *
 * @method Model|null getRecord()
 */
trait InteractsWithMeta
{
    /**
     * Hydrate the metadata form field from the record's stored metadata.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function fillMetaFormData(array $data): array
    {
        $data['meta'] = $this->getMetaRecord()->metaValues();

        return $data;
    }

    /**
     * Sync the metadata form field back into the record.
     */
    protected function persistMeta(): void
    {
        $this->getMetaRecord()->syncMeta($this->data['meta'] ?? []);
    }

    /**
     * Get the record being managed as a metadata-aware model.
     *
     * @return Model&Metable
     */
    private function getMetaRecord(): Metable
    {
        /** @var Model&Metable $record */
        $record = $this->getRecord();

        return $record;
    }
}
