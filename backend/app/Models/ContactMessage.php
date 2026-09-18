<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'company', 'project_type', 'budget_range', 'message', 'status', 'admin_notes', 'consented_at'];

    protected $hidden = ['email', 'phone', 'admin_notes'];

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
            'consented_at' => 'datetime',
        ];
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ContactAttachment::class);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}
