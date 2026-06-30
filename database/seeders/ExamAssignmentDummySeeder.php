<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamRoom;
use App\Models\ExamSession;
use App\Models\User;
use App\Models\UserDocument;
use App\Support\ProfessionRequirements;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExamAssignmentDummySeeder extends Seeder
{
    private const FIXED_ROOM_NAMES = [
        'CBT 1',
        'CBT 2',
        'CBT 3',
        'CBT 4',
        'IOT',
        'Data Science',
        'Animasi',
        'HPC',
        'SOC',
    ];

    private const GENERAL_DOCUMENTS = [
        'surat_lamaran' => 'Surat Lamaran',
        'cv_resume' => 'CV / Resume',
        'ijazah' => 'Scan Ijazah',
        'transkrip' => 'Transkrip Nilai',
        'ktp' => 'KTP',
        'surat_keterangan_sehat' => 'Surat Keterangan Sehat',
        'pas_foto' => 'Pas Foto',
    ];

    private const DUMMY_FILE_PATH = 'user-documents/local-dummy/dokumen-dummy.pdf';

    public function run(): void
    {
        Storage::disk('public')->put(self::DUMMY_FILE_PATH, "%PDF-1.4\n% Dummy dokumen lokal\n");

        DB::transaction(function (): void {
            $rooms = $this->fixedRooms();
            $this->seedExam($rooms);
            $this->purgeDummyRooms();
            $this->purgePreviousDummyUsers();
            $this->seedEligibleUsers();
        });
    }

    private function fixedRooms(): array
    {
        return ExamRoom::query()
            ->whereIn('name', self::FIXED_ROOM_NAMES)
            ->get()
            ->sortBy(fn (ExamRoom $room) => array_search($room->name, self::FIXED_ROOM_NAMES, true))
            ->values()
            ->all();
    }

    /**
     * @param array<int, ExamRoom> $rooms
     */
    private function seedExam(array $rooms): void
    {
        $exam = Exam::updateOrCreate(
            ['name' => 'Uji Kompetensi Dummy Pembagian Ruangan'],
            Exam::factory()->make([
                'exam_room_id' => $rooms[0]->id,
                'name' => 'Uji Kompetensi Dummy Pembagian Ruangan',
                'start_date' => Carbon::parse('2026-07-06')->toDateString(),
                'end_date' => Carbon::parse('2026-07-07')->toDateString(),
            ])->getAttributes(),
        );

        $exam->rooms()->sync(collect($rooms)->pluck('id')->all());

        collect([
            ['order' => 1, 'start_time' => '08:00', 'end_time' => '10:00'],
            ['order' => 2, 'start_time' => '10:30', 'end_time' => '12:30'],
        ])->each(fn (array $session) => ExamSession::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'order' => $session['order'],
            ],
            ExamSession::factory()->make([
                'exam_id' => $exam->id,
                ...$session,
            ])->getAttributes(),
        ));
    }

    private function seedEligibleUsers(): void
    {
        $professions = [
            null,
            'Dokter umum',
            'Ners',
            'Perawat D3',
            'Administrasi Perkantoran',
        ];

        for ($index = 1; $index <= 100; $index++) {
            $profession = $professions[($index - 1) % count($professions)];
            $submittedAt = Carbon::parse('2026-07-01 08:00:00')->addMinutes($index);
            $email = sprintf('local-ruang-%03d@pendaftaran.test', $index);

            $user = User::withTrashed()->updateOrCreate(
                ['email' => $email],
                User::factory()->activeUser()->make([
                    'name' => fake('id_ID')->name(),
                    'email' => $email,
                    'profesi' => $profession,
                    'phone_number' => fake('id_ID')->phoneNumber(),
                    'documents_submitted_at' => $submittedAt,
                    'deleted_at' => null,
                ])->getAttributes(),
            );

            $this->seedVerifiedDocuments($user, $profession, $submittedAt);
        }
    }

    private function purgePreviousDummyUsers(): void
    {
        $dummyUserIds = UserDocument::query()
            ->where('file_path', self::DUMMY_FILE_PATH)
            ->pluck('user_id');

        User::withTrashed()
            ->whereIn('id', $dummyUserIds)
            ->orWhere('email', 'like', 'local-ruang-%@pendaftaran.test')
            ->forceDelete();
    }

    private function purgeDummyRooms(): void
    {
        ExamRoom::query()
            ->where('name', 'like', 'Lokal Dummy %')
            ->delete();
    }

    private function seedVerifiedDocuments(User $user, ?string $profession, Carbon $submittedAt): void
    {
        $requiredDocuments = array_merge(
            self::GENERAL_DOCUMENTS,
            ProfessionRequirements::labelsFor($profession),
        );

        collect($requiredDocuments)->each(function (string $label, string $type) use ($user, $submittedAt): void {
            UserDocument::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'document_type' => $type,
                    'document_slot' => 1,
                ],
                UserDocument::factory()->verified()->make([
                    'user_id' => $user->id,
                    'document_type' => $type,
                    'document_name' => $label,
                    'file_path' => self::DUMMY_FILE_PATH,
                    'original_name' => str($type)->replace('_', '-')->append('.pdf')->toString(),
                    'uploaded_at' => $submittedAt->copy()->subMinutes(5),
                ])->getAttributes(),
            );
        });
    }
}
