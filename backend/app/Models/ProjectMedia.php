<?php

namespace App\Models;

use App\Enums\ProjectMediaType;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\ProjectMediaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMedia extends Model
{
    /** @use HasFactory<ProjectMediaFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = ['project_id', 'type', 'path', 'poster_path', 'caption', 'alt_text', 'mime_type', 'file_size', 'sort_order'];

    protected function casts(): array
    {
        return [
            'type' => ProjectMediaType::class,
            'caption' => 'array',
            'alt_text' => 'array',
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
