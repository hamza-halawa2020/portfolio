<?php

namespace App\Models;

use Database\Factories\ProjectViewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectView extends Model
{
    /** @use HasFactory<ProjectViewFactory> */
    use HasFactory;

    protected $fillable = ['project_id', 'visitor_id_hash', 'ip_hash', 'user_agent_hash', 'viewed_on', 'viewed_at'];

    protected $hidden = ['visitor_id_hash', 'ip_hash', 'user_agent_hash'];

    protected function casts(): array
    {
        return [
            'viewed_on' => 'date',
            'viewed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
