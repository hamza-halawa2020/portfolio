<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = ['title', 'company', 'location', 'description', 'starts_at', 'ends_at', 'is_current', 'sort_order'];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'company' => 'array',
            'location' => 'array',
            'description' => 'array',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_current' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('starts_at');
    }
}
