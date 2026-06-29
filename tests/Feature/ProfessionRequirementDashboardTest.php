<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDocument;
use App\Support\ProfessionRequirements;
use Illuminate\Support\Carbon;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ProfessionRequirementDashboardTest extends TestCase
{
    public function test_doctor_special_requirements_are_shown_before_general_documents(): void
    {
        $specialRequirement = ProfessionRequirements::for('Dokter umum');
        $uploadedDocument = new UserDocument([
            'user_id' => 1,
            'document_type' => 'dokter_ijazah_s1_profesi',
            'document_slot' => 1,
            'document_name' => 'Ijazah S1 dan Profesi',
            'file_path' => 'user-documents/1/ijazah.pdf',
            'original_name' => 'ijazah.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status' => 'uploaded',
        ]);
        $uploadedDocument->id = 10;
        $uploadedDocument->exists = true;
        $generalDocument = new UserDocument([
            'user_id' => 1,
            'document_type' => 'surat_lamaran',
            'document_slot' => 1,
            'document_name' => 'Surat Lamaran',
            'file_path' => 'user-documents/1/surat-lamaran.pdf',
            'original_name' => 'surat-lamaran.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status' => 'uploaded',
        ]);
        $generalDocument->id = 11;
        $generalDocument->exists = true;

        $view = $this
            ->actingAs($this->userWithProfession('Dokter umum'))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect([
                    'dokter_ijazah_s1_profesi' => collect([$uploadedDocument]),
                    'surat_lamaran' => collect([$generalDocument]),
                ]),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Dokter Umum)',
            'Ijazah S1 dan Profesi',
            'Sertifikat STR Aktif',
            'Sertifikat ACLS/ATLS',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);
        $view->assertSee('Selesai pada 27 Jun 2026');
        $view->assertSee('data-multiple="true"', false);
        $view->assertSee('data-max-files="5"', false);
        $view->assertSee('Hapus');
        $view->assertSee(route('user.documents.destroy', $uploadedDocument), false);
        $view->assertSee(route('user.documents.destroy', $generalDocument), false);
    }

    public function test_ners_special_requirements_are_shown_before_unchanged_general_documents(): void
    {
        $specialRequirement = ProfessionRequirements::for('Ners');

        $view = $this
            ->actingAs($this->userWithProfession('Ners'))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (NERS)',
            'Ijazah S1 dan Profesi',
            'STR Aktif',
            'Sertifikat BCLS/BTCLS Aktif',
            'Sertifikat (ICU, PICU, PERINA, HD)',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);
        $view->assertSee('class="document-card document-card-wide"', false);

        $markup = (string) $view;
        $this->assertMatchesRegularExpression(
            '/<article[^>]*>.*?Sertifikat \(ICU, PICU, PERINA, HD\).*?data-max-files=""/s',
            $markup,
        );
    }

    public function test_perawat_d3_special_requirements_are_shown_before_unchanged_general_documents(): void
    {
        $specialRequirement = ProfessionRequirements::for('Perawat D3');

        $view = $this
            ->actingAs($this->userWithProfession('Perawat D3'))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Perawat D3)',
            'Ijazah D3',
            'STR Aktif',
            'Sertifikat BCLS/BTCLS Aktif',
            'Sertifikat (ICU, PICU, PERINA, HD)',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);
        $view->assertSee('class="document-card document-card-wide"', false);
    }

    public function test_bidan_d4_s1_profesi_special_requirements_are_shown_before_unchanged_general_documents(): void
    {
        $profession = 'Bidan D4/S1 + Profesi';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Bidan D4/S1 + Profesi)',
            'Ijazah D4/S1 + Profesi',
            'STR Aktif',
            'Sertifikat APN, PPGDON, dan MU Aktif',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);
    }

    public function test_bidan_d3_special_requirements_are_shown_before_unchanged_general_documents(): void
    {
        $profession = 'Bidan D3';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Bidan D3)',
            'Ijazah D3',
            'STR Aktif',
            'Sertifikat APN, PPGDON, dan MU Aktif',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);
    }

    public function test_pranata_komputer_it_shows_two_special_requirements_before_unchanged_general_documents(): void
    {
        $profession = 'Pranata Komputer IT';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Pranata Komputer IT)',
            'Ijazah Min D3 Komputer',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(2, substr_count($specialMarkup, '<article'));
        $this->assertStringNotContainsString('document-card-wide', $specialMarkup);
    }

    public function test_akuntansi_shows_two_special_requirements_before_unchanged_general_documents(): void
    {
        $profession = 'Akuntansi';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Akuntansi)',
            'Ijazah Min D3 Akuntansi',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(2, substr_count($specialMarkup, '<article'));
        $this->assertStringNotContainsString('document-card-wide', $specialMarkup);
    }

    public function test_ttk_shows_three_special_requirements_with_a_wide_certificate_card(): void
    {
        $profession = 'TTK (Asisten Apoteker)';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (TTK/Asisten Apoteker)',
            'Ijazah D3',
            'STR Aktif',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(3, substr_count($specialMarkup, '<article'));
        $this->assertSame(1, substr_count($specialMarkup, 'document-card-wide'));
    }

    public function test_administrasi_perkantoran_shows_two_side_by_side_requirements(): void
    {
        $profession = 'Administrasi Perkantoran';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Administrasi Perkantoran)',
            'Ijazah Min D3 Semua Jurusan',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(2, substr_count($specialMarkup, '<article'));
        $this->assertStringNotContainsString('document-card-wide', $specialMarkup);
    }

    public function test_teknik_pendingin_shows_two_side_by_side_special_requirements(): void
    {
        $profession = 'Teknik Pendingin';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (Teknik Pendingin)',
            'Ijazah D3 Teknik Pendingin/Refrigerasi',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(2, substr_count($specialMarkup, '<article'));
        $this->assertStringNotContainsString('document-card-wide', $specialMarkup);
    }

    public function test_bdrs_shows_three_special_requirements_with_a_wide_certificate_card(): void
    {
        $profession = 'BDRS';
        $specialRequirement = ProfessionRequirements::for($profession);

        $view = $this
            ->actingAs($this->userWithProfession($profession))
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => false,
                'errors' => new ViewErrorBag,
            ]);

        $view->assertSeeInOrder([
            'Persyaratan Khusus (BDRS)',
            'Ijazah D3',
            'STR Aktif',
            'Sertifikat Lainnya',
            'Persyaratan Umum',
            'Surat Lamaran',
        ]);

        $specialMarkup = explode('</section>', explode('<section class="special-requirements"', (string) $view, 2)[1], 2)[0];

        $this->assertSame(3, substr_count($specialMarkup, '<article'));
        $this->assertSame(1, substr_count($specialMarkup, 'document-card-wide'));
    }

    public function test_submitted_documents_are_rendered_read_only(): void
    {
        $user = $this->userWithProfession('Dokter umum');
        $user->documents_submitted_at = Carbon::parse('2026-06-27 11:00:00');
        $specialRequirement = ProfessionRequirements::for('Dokter umum');

        $view = $this
            ->actingAs($user)
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => $specialRequirement,
                'specialDocuments' => $specialRequirement['documents'],
                'uploadedDocuments' => collect(),
                'documentsSubmitted' => true,
                'errors' => new ViewErrorBag,
            ]);

        $markup = explode('<script src=', (string) $view)[0];

        $this->assertStringContainsString('Sudah Dikirim', $markup);
        $this->assertStringNotContainsString('Dokumen sudah dikirim dan tidak dapat diubah.', $markup);
        $this->assertStringNotContainsString('delete-document-button', $markup);
        $this->assertStringNotContainsString('document-dropzone', $markup);
        $this->assertStringNotContainsString('Kirim Dokumen', $markup);
        $this->assertStringNotContainsString('Kartu Uji Kompetensi', $markup);
        $this->assertStringNotContainsString('Unduh Kartu Uji Kompetensi', $markup);
        $this->assertStringNotContainsString('exam-download-box', $markup);
        $this->assertStringNotContainsString('Butuh Bantuan?', $markup);
        $this->assertStringNotContainsString('hrd@rsud-reg.com', $markup);
    }

    public function test_verified_documents_move_the_current_status_to_uji_kompetensi(): void
    {
        $user = $this->userWithProfession('Profesi Lain');
        $verifiedDocument = new UserDocument([
            'user_id' => $user->id,
            'document_type' => 'surat_lamaran',
            'document_slot' => 1,
            'document_name' => 'Surat Lamaran',
            'file_path' => 'user-documents/1/surat-lamaran.pdf',
            'original_name' => 'surat-lamaran.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status' => 'verified',
        ]);
        $verifiedDocument->id = 99;
        $verifiedDocument->exists = true;

        $view = $this
            ->actingAs($user)
            ->view('user.dashboard', [
                'documents' => $this->generalDocuments(),
                'specialRequirement' => null,
                'specialDocuments' => [],
                'uploadedDocuments' => collect([
                    'surat_lamaran' => collect([$verifiedDocument]),
                ]),
                'documentsSubmitted' => true,
                'errors' => new ViewErrorBag,
            ]);

        $markup = (string) $view;

        $this->assertMatchesRegularExpression('/<div class="timeline-item done">.*?<h3>Verifikasi Admin<\/h3>/s', $markup);
        $this->assertMatchesRegularExpression('/<div class="timeline-item current">.*?<h3>Uji Kompetensi<\/h3>/s', $markup);
        $this->assertStringContainsString('Kartu Uji Kompetensi', $markup);
        $this->assertStringContainsString('Verifikasi admin selesai. Unduh kartu uji kompetensi', $markup);
        $this->assertMatchesRegularExpression('/<button[^>]+class="exam-download-button"[^>]+disabled/', $markup);
    }

    private function userWithProfession(string $profession): User
    {
        $user = new User([
            'name' => 'User Pengujian',
            'profesi' => $profession,
            'email' => 'user@example.com',
            'role' => 'user',
            'status' => 'Active',
        ]);
        $user->id = 1;
        $user->exists = true;
        $user->created_at = Carbon::parse('2026-06-27 10:00:00');

        return $user;
    }

    private function generalDocuments(): array
    {
        return [
            'surat_lamaran' => [
                'title' => 'Surat Lamaran',
                'description' => 'Surat resmi lamaran untuk posisi yang dituju.',
                'format' => 'PDF/JPG/PNG',
                'mimes' => 'pdf,jpg,jpeg,png',
                'tone' => 'blue',
                'icon' => 'document',
            ],
        ];
    }
}
