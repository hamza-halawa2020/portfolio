<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = ['name', 'group', 'level', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'level' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
