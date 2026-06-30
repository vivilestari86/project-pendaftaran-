<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDocument>
 */
class UserDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'document_type' => 'surat_lamaran',
            'document_slot' => 1,
            'document_name' => 'Surat Lamaran',
            'file_path' => 'user-documents/local-dummy/dokumen-dummy.pdf',
            'original_name' => 'dokumen-dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 128,
            'status' => 'uploaded',
            'uploaded_at' => now(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verified',
            'uploaded_at' => now(),
        ]);
    }
}
