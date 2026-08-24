<?php

namespace Cachet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (self $tag): void {
            $tag->slug = Str::slug($tag->name);
        });
    }
}
