<?php

namespace Cachet\Models;

use Cachet\Cachet;
use Cachet\Database\Factories\UpdateFactory;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Status;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;

/**
 * @template TUser of Authenticatable
 *
 * @property int $id
 * @property ?IncidentStatusEnum $status
 * @property string $message
 * @property ?int $user_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property class-string<Incident|Schedule> $updateable_type
 * @property int $updateable_id
 * @property Incident|Schedule $updateable
 * @property TUser $user
 *
 * @method static UpdateFactory factory($count = null, $state = [])
 */
class Update extends Model
{
    /** @use HasFactory<UpdateFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'status' => IncidentStatusEnum::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'status',
        'message',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::saved(function (Update $update): void {
            $update->forgetRssFeed();
            Status::flush();
        });

        static::deleted(function (Update $update): void {
            $update->forgetRssFeed();
            Status::flush();
        });
    }

    /**
     * Get the resource that the update belongs to.
     */
    public function updateable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('cachet.user_model'));
    }

    /**
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Cachet::markdown($this->message);
    }

    /**
     * Clear the RSS feed when an incident update changes its content.
     */
    private function forgetRssFeed(): void
    {
        if ($this->updateable instanceof Incident) {
            Cache::forget('cachet::rss-feed');
            Cache::forget('cachet::rss-feed-last-modified');
        }
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UpdateFactory::new();
    }
}
