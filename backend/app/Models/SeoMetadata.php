<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\SeoMetadataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    /** @use HasFactory<SeoMetadataFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $table = 'seo_metadata';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'page_key',
        'title',
        'description',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_path',
        'robots_index',
        'robots_follow',
        'structured_data',
        'redirect_url',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'og_title' => 'array',
            'og_description' => 'array',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'structured_data' => 'array',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
