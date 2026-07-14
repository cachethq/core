<?php

namespace Cachet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property int $meta_id
 * @property string $meta_type
 * @property-read Model $meta
 */
class Meta extends Model
{
    /** @var string */
    protected $table = 'meta';

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'json',
    ];

    /** @var list<string> */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get the parent model that owns the metadata.
     *
     * @return MorphTo<Model, $this>
     */
    public function meta(): MorphTo
    {
        return $this->morphTo();
    }
}
