<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = ['name', 'slug'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'slug' => 'array',
        ];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class);
    }
}
