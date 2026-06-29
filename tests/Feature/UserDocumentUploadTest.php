<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserDocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite extension is not available.');
        }

        parent::setUp();
    }

    public function test_user_can_upload_document_and_save_it_to_database(): void
    {
        Storage::fake('public');

        $user = User::factory()->activeUser()->create();
        $file = UploadedFile::fake()->create('surat-lamaran.pdf', 128, 'application/pdf');

        $response = $this
            ->actingAs($user)
            ->post(route('user.documents.store'), [
                'documents' => [
                    'surat_lamaran' => $file,
                ],
            ]);

        $response->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('user_documents', [
            'user_id' => $user->id,
            'document_type' => 'surat_lamaran',
            'document_name' => 'Surat Lamaran',
            'original_name' => 'surat-lamaran.pdf',
            'status' => 'uploaded',
        ]);

        $document = UserDocument::where('user_id', $user->id)
            ->where('document_type', 'surat_lamaran')
            ->firstOrFail();

        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_ners_can_upload_more_than_four_competency_certificates(): void
    {
        Storage::fake('public');

        $user = User::factory()->activeUser()->create([
            'profesi' => 'Ners',
        ]);

        foreach (range(1, 5) as $index) {
            $response = $this
                ->actingAs($user)
                ->postJson(route('user.documents.upload', 'ners_sertifikat_kompetensi'), [
                    'file' => UploadedFile::fake()->create("sertifikat-{$index}.pdf", 128, 'application/pdf'),
                ]);

            $response->assertOk();
        }

        $this->assertSame(
            5,
            UserDocument::where('user_id', $user->id)
                ->where('document_type', 'ners_sertifikat_kompetensi')
                ->count(),
        );
    }
}
