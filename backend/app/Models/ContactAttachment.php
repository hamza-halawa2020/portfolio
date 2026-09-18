<?php

namespace App\Models;

use Database\Factories\ContactAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactAttachment extends Model
{
    /** @use HasFactory<ContactAttachmentFactory> */
    use HasFactory;

    protected $fillable = ['contact_message_id', 'path', 'original_name', 'mime_type', 'file_size'];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function contactMessage(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class);
    }
}
