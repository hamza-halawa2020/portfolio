<?php

namespace App\Models;

use Database\Factories\ProjectLikeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLike extends Model
{
    /** @use HasFactory<ProjectLikeFactory> */
    use HasFactory;

    protected $fillable = ['project_id', 'visitor_id_hash', 'ip_hash', 'user_agent_hash', 'liked_at'];

    protected $hidden = ['visitor_id_hash', 'ip_hash', 'user_agent_hash'];

    protected function casts(): array
    {
        return [
            'liked_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
