<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('user', fn(Builder $builder) => $builder
            ->whereHas('users', fn ($q) => $q->where('users.id', auth()->id())));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class)->orderBy('arrival_at');
    }
    // https://nominatim.openstreetmap.org/search?viewbox=6.0493,50.2411,6.9749,51.0561&format=json&q=[camp_site]&bounded=1&countrycodes=de&limit=50
}
