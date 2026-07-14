<?php

namespace Cachet\Concerns;

use Cachet\Models\Meta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @phpstan-require-extends Model
 */
trait HasMeta
{
    /**
     * Delete the metadata when the owning model is removed.
     *
     * Soft-deleted models still exist, so they keep their metadata for a
     * restore; the rows are only purged once the model is hard or force
     * deleted and gone from the database.
     */
    protected static function bootHasMeta(): void
    {
        static::deleted(function (self $model): void {
            if ($model->exists) {
                return;
            }

            $model->meta()->delete();
        });
    }

    /**
     * Get the metadata assigned to the model.
     *
     * @return MorphMany<Meta, $this>
     */
    public function meta(): MorphMany
    {
        return $this->morphMany(Meta::class, 'meta');
    }

    /**
     * Get the metadata as a key/value array.
     *
     * @return array<string, mixed>
     */
    public function metaValues(): array
    {
        return $this->meta->pluck('value', 'key')->all();
    }

    /**
     * Sync the given key/value pairs into the model's metadata.
     *
     * Keys not present in the given array are removed, keeping the metadata
     * table in step with the provided values. Values are stored as JSON so
     * they retain their original type when read back.
     *
     * @param  array<string, mixed>  $meta
     */
    public function syncMeta(array $meta): void
    {
        $keys = array_map(strval(...), array_keys($meta));

        $this->meta()->whereNotIn('key', $keys)->delete();

        foreach ($meta as $key => $value) {
            $this->meta()->updateOrCreate(
                ['key' => (string) $key],
                ['value' => $value],
            );
        }

        $this->unsetRelation('meta');
    }
}
