<?php

namespace Database\Factories;

use App\Models\ContactAttachment;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContactAttachment> */
class ContactAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contact_message_id' => ContactMessage::factory(),
            'path' => 'contact/demo-brief.pdf',
            'original_name' => 'fictional-project-brief.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10_000, 200_000),
        ];
    }
}
