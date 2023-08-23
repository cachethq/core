<?php

namespace Cachet\Models;

use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentGroup extends Model
{
    use HasFactory;

    protected $casts = [
        'order' => 'int',
        'visible' => ResourceVisibilityEnum::class,
    ];

    /**
     * Get the components in the group.
     */
    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
