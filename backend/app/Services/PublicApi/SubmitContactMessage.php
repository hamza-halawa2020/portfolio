<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\ContactSubmissionData;
use App\Enums\ContactMessageStatus;
use App\Events\PublicApi\ContactMessageSubmitted;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SubmitContactMessage
{
    public function handle(ContactSubmissionData $data): ContactMessage
    {
        $this->rejectRecentDuplicate($data);

        return DB::transaction(function () use ($data): ContactMessage {
            $message = ContactMessage::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'company' => $data->company,
                'project_type' => $data->projectType,
                'budget_range' => $data->budgetRange,
                'message' => $data->message,
                'status' => ContactMessageStatus::New,
                'consented_at' => now(),
            ]);

            ContactMessageSubmitted::dispatch($message->id);

            return $message;
        });
    }

    private function rejectRecentDuplicate(ContactSubmissionData $data): void
    {
        $exists = ContactMessage::query()
            ->where('email', $data->email)
            ->where('created_at', '>=', now()->subHour())
            ->where('message', $data->message)
            ->exists();

        throw_if($exists, ValidationException::withMessages([
            'submission' => 'Submission received successfully.',
        ]));
    }
}
