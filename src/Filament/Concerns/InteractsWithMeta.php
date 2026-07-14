<?php

namespace Cachet\Filament\Concerns;

use Cachet\Concerns\Metable;
use Illuminate\Database\Eloquent\Model;

/**
 * Persists a `meta` KeyValue form field into the model's polymorphic metadata.
 *
 * Pages using this concern must pass the validated form state through
 * extractMetaFormData() (from mutateFormDataBeforeCreate or
 * mutateFormDataBeforeSave) and call persistMeta() after the record is
 * created or saved. The metadata must be read from the validated form state
 * rather than the page's raw Livewire data, as the KeyValue field only
 * casts its state to a key/value map during form dehydration.
 *
 * @method Model|null getRecord()
 */
trait InteractsWithMeta
{
    /** @var array<string, mixed> */
    protected array $metaFormState = [];

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
     * Pull the metadata out of the validated form state, keeping it for
     * persistMeta() and preventing it from reaching mass assignment.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function extractMetaFormData(array $data): array
    {
        $this->metaFormState = $data['meta'] ?? [];

        unset($data['meta']);

        return $data;
    }

    /**
     * Sync the extracted metadata form state back into the record.
     */
    protected function persistMeta(): void
    {
        $this->getMetaRecord()->syncMeta($this->metaFormState);
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
