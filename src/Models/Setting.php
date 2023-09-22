<?php

declare(strict_types=1);

namespace Cachet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Properties
 *
 * @property-read int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends Model
{
    use HasFactory;
}
