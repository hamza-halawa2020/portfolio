<?php

namespace App\Services\Admin\Content;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ContactInboxService
{
    public function __construct(
        private readonly PublicContentCacheInvalidator $cacheInvalidator,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ContactMessage $message, array $data): ContactMessage
    {
        return DB::transaction(function () use ($message, $data): ContactMessage {
            $message->forceFill([
                'status' => Arr::get($data, 'status', $message->status),
                'admin_notes' => Arr::get($data, 'admin_notes', $message->admin_notes),
            ])->save();

            $this->cacheInvalidator->contactInbox();

            return $message->refresh();
        });
    }

    public function markRead(ContactMessage $message): ContactMessage
    {
        return DB::transaction(function () use ($message): ContactMessage {
            if ($message->status === ContactMessageStatus::New) {
                $message->forceFill(['status' => ContactMessageStatus::Read])->save();
            }

            $this->cacheInvalidator->contactInbox();

            return $message->refresh();
        });
    }
}
