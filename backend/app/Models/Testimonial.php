<?php

namespace App\Models;

use App\Enums\TestimonialStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'company',
        'position',
        'profile_image_path',
        'content',
        'rating',
        'contact_email',
        'status',
        'is_featured',
        'consented_at',
        'reviewed_at',
    ];

    protected $hidden = ['contact_email', 'reviewed_at'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'rating' => 'integer',
            'status' => TestimonialStatus::class,
            'is_featured' => 'boolean',
            'consented_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', TestimonialStatus::Approved);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
