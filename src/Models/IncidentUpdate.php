<?php

namespace Cachet\Models;

use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentUpdate extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => IncidentStatusEnum::class,
    ];

    protected $fillable = [
        'status',
        'message',
        'user_id',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
