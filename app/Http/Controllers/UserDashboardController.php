<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamRoom;
use App\Models\ExamSession;
use App\Models\User;
use App\Models\UserDocument;
use App\Support\ProfessionRequirements;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class UserDashboardController extends Controller
{
    private const EXAM_CENTER = 'Politeknik Negeri Indramayu';
    private const EXAM_LOCATION = 'Polindra - Gedung Student Center';
    private const EXAM_ADDRESS = 'Jl. Raya Lohbener Lama No. 8, Kec. Lohbener, Kabupaten Indramayu, Jawa Barat';

    private const DOCUMENTS = [
        'surat_lamaran' => [
            'title' => 'Surat Lamaran',
            'description' => 'Surat resmi lamaran untuk posisi yang dituju.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'blue',
            'icon' => 'document',
        ],
        'cv_resume' => [
            'title' => 'CV / Resume',
            'description' => 'Resume profesional berisi pengalaman dan keahlian.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'gold',
            'icon' => 'id-card',
        ],
        'ijazah' => [
            'title' => 'Scan Ijazah',
            'description' => 'Scan ijazah terakhir yang terbaca jelas.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'indigo',
            'icon' => 'graduation',
        ],
        'transkrip' => [
            'title' => 'Transkrip Nilai',
            'description' => 'Scan transkrip nilai lengkap yang terbaca jelas.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'blue',
            'icon' => 'document',
        ],
        'ktp' => [
            'title' => 'KTP',
            'description' => 'Scan Kartu Tanda Penduduk/e-KTP yang masih berlaku.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'gray',
            'icon' => 'identity',
        ],
        'surat_keterangan_sehat' => [
            'title' => 'Surat Keterangan Sehat',
            'description' => 'Surat keterangan sehat terbaru dari rumah sakit.',
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'red',
            'icon' => 'medical',
        ],
        'pas_foto' => [
            'title' => 'Pas Foto',
            'description' => 'Pas foto 4x6 terbaru dengan latar formal.',
            'format' => 'JPG/PNG',
            'mimes' => 'jpg,jpeg,png',
            'tone' => 'olive',
            'icon' => 'camera',
            'wide' => true,
        ],
    ];

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $specialRequirement = ProfessionRequirements::for($user->profesi);
        $uploadedDocuments = $user
            ->documents()
            ->get()
            ->groupBy('document_type');
        $examCard = $this->resolveExamCardForUser($user);

        return view('user.dashboard', [
            'documents' => self::DOCUMENTS,
            'specialRequirement' => $specialRequirement,
            'specialDocuments' => $specialRequirement['documents'] ?? [],
            'uploadedDocuments' => $uploadedDocuments,
            'documentsSubmitted' => $user->documents_submitted_at !== null,
            'examCard' => $examCard,
        ]);
    }

    public function downloadExamCard(): Response|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $examCard = $this->resolveExamCardForUser($user);

        if ($examCard === null) {
            return redirect()
                ->to(route('user.dashboard') . '#dashboard-top')
                ->withErrors(['exam_card' => 'Kartu ujian belum tersedia. Tunggu verifikasi dan penjadwalan admin.']);
        }

        $fileName = 'kartu-uji-kompetensi-' . Str::slug($user->name ?: 'peserta') . '.pdf';
        $pdf = $this->buildExamCardPdf($user, $examCard);

        return response(
            $pdf,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => (string) strlen($pdf),
            ],
        );
    }

    public function storeDocuments(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if($user->documents_submitted_at !== null, 403, 'Dokumen yang sudah dikirim tidak dapat diubah.');

        $documents = $this->documentsFor($user->profesi);
        $rules = [];

        foreach ($documents as $key => $document) {
            if ($document['multiple'] ?? false) {
                $rules["documents.$key"] = 'nullable|array'
                    .(isset($document['max_files']) ? '|max:'.$document['max_files'] : '');
                $rules["documents.$key.*"] = "file|mimes:{$document['mimes']}|max:5120";

                continue;
            }

            $rules["documents.$key"] = "nullable|file|mimes:{$document['mimes']}|max:5120";
        }

        $validator = validator($request->all(), $rules, [
            'documents.*.array' => 'Dokumen harus berupa daftar file yang valid.',
            'documents.*.file' => 'Dokumen harus berupa file yang valid.',
            'documents.*.*.file' => 'Dokumen harus berupa file yang valid.',
            'documents.*.mimes' => 'Format dokumen tidak sesuai.',
            'documents.*.*.mimes' => 'Format dokumen tidak sesuai.',
            'documents.*.max' => 'Ukuran dokumen maksimal 5MB.',
            'documents.*.*.max' => 'Ukuran dokumen maksimal 5MB.',
        ]);

        $validator->after(function (Validator $validator) use ($documents, $request): void {
            $hasUploadedFile = collect(array_keys($documents))
                ->contains(fn (string $key): bool => $request->hasFile("documents.$key"));

            if (! $hasUploadedFile) {
                $validator->errors()->add('documents', 'Pilih minimal satu dokumen untuk diupload.');
            }
        });

        $validator->validate();

        $savedCount = 0;

        foreach ($documents as $key => $document) {
            if (! $request->hasFile("documents.$key")) {
                continue;
            }

            $files = $document['multiple'] ?? false
                ? $request->file("documents.$key", [])
                : [$request->file("documents.$key")];

            UserDocument::where('user_id', $user->id)
                ->where('document_type', $key)
                ->get()
                ->each(fn (UserDocument $existingDocument) => $this->deleteDocumentFile($existingDocument));

            foreach (array_values($files) as $index => $file) {
                $path = $file->store("user-documents/{$user->id}", 'public');

                UserDocument::create([
                    'user_id' => $user->id,
                    'document_type' => $key,
                    'document_slot' => $index + 1,
                    'document_name' => $document['title'],
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'status' => 'uploaded',
                    'uploaded_at' => now(),
                ]);

                $savedCount++;
            }
        }

        return redirect()
            ->route('user.dashboard')
            ->with('success', "{$savedCount} dokumen berhasil disimpan.");
    }

    public function uploadDocument(Request $request, string $documentType): JsonResponse
    {
        $user = Auth::user();
        abort_if($user->documents_submitted_at !== null, 403, 'Dokumen yang sudah dikirim tidak dapat diubah.');

        $documents = $this->documentsFor($user->profesi);

        abort_unless(array_key_exists($documentType, $documents), 404);

        $document = $documents[$documentType];

        $validated = $request->validate([
            'file' => "required|file|mimes:{$document['mimes']}|max:5120",
        ], [
            'file.required' => 'Pilih dokumen untuk diupload.',
            'file.file' => 'Dokumen harus berupa file yang valid.',
            'file.mimes' => 'Format dokumen tidak sesuai.',
            'file.max' => 'Ukuran dokumen maksimal 5MB.',
        ]);

        $existingDocuments = UserDocument::where('user_id', $user->id)
            ->where('document_type', $documentType)
            ->orderBy('document_slot')
            ->get();

        if ($document['multiple'] ?? false) {
            $maxFiles = $document['max_files'] ?? null;

            if ($maxFiles !== null && $existingDocuments->count() >= $maxFiles) {
                throw ValidationException::withMessages([
                    'file' => "Maksimal {$maxFiles} file untuk {$document['title']}.",
                ]);
            }

            $usedSlots = $existingDocuments->pluck('document_slot')->all();
            $documentSlot = 1;

            while (in_array($documentSlot, $usedSlots, true)) {
                $documentSlot++;
            }
        } else {
            $existingDocuments
                ->each(fn (UserDocument $existingDocument) => $this->deleteDocumentFile($existingDocument));
            $documentSlot = 1;
        }

        $file = $validated['file'];
        $path = $file->store("user-documents/{$user->id}", 'public');

        $savedDocument = UserDocument::create([
            'user_id' => $user->id,
            'document_type' => $documentType,
            'document_slot' => $documentSlot,
            'document_name' => $document['title'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'uploaded',
            'uploaded_at' => now(),
        ]);

        return response()->json([
            'message' => "{$document['title']} berhasil diupload.",
            'document' => [
                'name' => $savedDocument->original_name,
                'url' => route('user.documents.show', $savedDocument),
                'delete_url' => route('user.documents.destroy', $savedDocument),
            ],
        ]);
    }

    public function destroyDocument(UserDocument $document): JsonResponse
    {
        abort_unless($document->user_id === Auth::id(), 403);
        abort_if(Auth::user()->documents_submitted_at !== null, 403, 'Dokumen yang sudah dikirim tidak dapat diubah.');

        $this->deleteDocumentFile($document);

        return response()->json([
            'message' => 'Dokumen berhasil dihapus.',
        ]);
    }

    public function submitDocuments(): RedirectResponse
    {
        $user = Auth::user();
        $documents = $this->documentsFor($user->profesi);
        $requiredDocumentTypes = array_keys($documents);

        if ($user->documents_submitted_at === null) {
            $uploadedDocumentTypes = $user->documents()
                ->whereIn('document_type', $requiredDocumentTypes)
                ->distinct()
                ->pluck('document_type');

            $missingDocuments = collect($requiredDocumentTypes)->diff($uploadedDocumentTypes);

            if ($missingDocuments->isNotEmpty()) {
                return redirect()
                    ->to(route('user.dashboard').'#dashboard-top')
                    ->withErrors(['documents' => 'Lengkapi seluruh persyaratan sebelum mengirim dokumen.']);
            }

            $user->forceFill([
                'documents_submitted_at' => now(),
            ])->save();
        }

        $message = ProfessionRequirements::documentsFor($user->profesi) !== []
            ? 'Persyaratan khusus dan persyaratan umum berhasil dikirim.'
            : 'Persyaratan umum berhasil dikirim.';

        return redirect()
            ->to(route('user.dashboard').'#dashboard-top')
            ->with('success', $message);
    }

    public function showDocument(UserDocument $document): BinaryFileResponse
    {
        abort_unless($document->user_id === Auth::id(), 403);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($document->file_path),
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.addslashes($document->original_name).'"',
            ],
        );
    }

    private function deleteDocumentFile(UserDocument $document): void
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
    }

    private function documentsFor(?string $profession): array
    {
        return array_merge(self::DOCUMENTS, ProfessionRequirements::documentsFor($profession));
    }

    private function resolveExamCardForUser(User $user): ?array
    {
        if (! $this->isEligibleForExamCard($user)) {
            return null;
        }

        $eligibleUsers = $this->eligibleUsersForExamCard();
        $position = $eligibleUsers->search(fn (User $candidate): bool => $candidate->id === $user->id);

        if ($position === false) {
            return null;
        }

        $slotPointer = (int) $position;

        foreach ($this->examSlots() as $slot) {
            if ($slotPointer < $slot['capacity']) {
                $seatNumber = $slotPointer + 1;

                return [
                    'exam' => $slot['exam'],
                    'session' => $slot['session'],
                    'room' => $slot['room'],
                    'date' => $slot['date'],
                    'seat_number' => $seatNumber,
                    'exam_number' => $this->examNumber(
                        $slot['exam'],
                        $slot['session'],
                        $slot['room'],
                        $user,
                        $seatNumber,
                    ),
                    'downloaded_at' => now(),
                ];
            }

            $slotPointer -= $slot['capacity'];
        }

        return null;
    }

    private function isEligibleForExamCard(User $user): bool
    {
        if ($user->status !== 'Active' || $user->documents_submitted_at === null) {
            return false;
        }

        $requiredDocumentTypes = array_keys($this->documentsFor($user->profesi));
        $uploadedDocuments = $user->relationLoaded('documents')
            ? $user->documents
            : $user->documents()->get();

        $verifiedCount = $uploadedDocuments
            ->whereIn('document_type', $requiredDocumentTypes)
            ->where('status', 'verified')
            ->pluck('document_type')
            ->unique()
            ->count();

        return $verifiedCount === count($requiredDocumentTypes);
    }

    private function eligibleUsersForExamCard()
    {
        return User::query()
            ->where('role', 'user')
            ->where('status', 'Active')
            ->whereNotNull('documents_submitted_at')
            ->with('documents')
            ->get()
            ->filter(fn (User $user): bool => $this->isEligibleForExamCard($user))
            ->sortBy([
                ['documents_submitted_at', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }

    private function examSlots()
    {
        return Exam::query()
            ->with(['sessions', 'rooms', 'room'])
            ->orderBy('start_date')
            ->orderBy('id')
            ->get()
            ->flatMap(function (Exam $exam) {
                $rooms = $exam->rooms
                    ->whenEmpty(fn ($collection) => $exam->room ? $collection->push($exam->room) : $collection)
                    ->sortBy('name')
                    ->values();

                return $exam->sessions->flatMap(function (ExamSession $session) use ($exam, $rooms) {
                    return $this->expandExamDates($exam)->flatMap(function (Carbon $date) use ($exam, $session, $rooms) {
                        return $rooms->map(function (ExamRoom $room) use ($exam, $session, $date) {
                            return [
                                'exam' => $exam,
                                'session' => $session,
                                'room' => $room,
                                'date' => $date->copy(),
                                'capacity' => max((int) $room->capacity, 0),
                            ];
                        });
                    });
                });
            })
            ->filter(fn (array $slot): bool => $slot['capacity'] > 0)
            ->values();
    }

    private function examNumber(
        Exam $exam,
        ExamSession $session,
        ExamRoom $room,
        User $user,
        int $seatNumber,
    ): string {
        return sprintf(
            'UK-%03d-%02d-%03d-%03d',
            $exam->id,
            $session->order,
            $room->id,
            $seatNumber + ($user->id % 100)
        );
    }

    private function buildExamCardPdf(User $user, array $examCard): string
    {
        $date = $examCard['date']->copy()->locale('id');
        $dayName = Str::upper($date->translatedFormat('l'));
        $dateLabel = Str::upper($date->translatedFormat('d F Y'));
        $timeLabel = $examCard['session']->start_time?->format('H:i') . ' - ' . $examCard['session']->end_time?->format('H:i') . ' WIB';
        $roomLabel = $this->roomDisplayName($examCard['room']);
        $locationLabel = $this->locationLabel($examCard['room']);
        $profilePhoto = $this->resolveProfilePhotoForPdf($user);
        $professionLabel = $user->profesi ? Str::title($user->profesi) : 'Peserta Terverifikasi';
        $headingColor = [0.10, 0.19, 0.42];
        $textColor = [0.12, 0.15, 0.20];
        $mutedColor = [0.41, 0.47, 0.58];
        $lineColor = [0.84, 0.88, 0.95];
        $blueBar = [0.18, 0.33, 0.74];
        $blueSoft = [0.96, 0.98, 1.00];

        $stream = [];
        $stream[] = '1 1 1 rg 18 18 559 806 re f';
        $stream[] = '0.86 0.89 0.95 RG 0.8 w 18 18 559 806 re S';
        $stream[] = sprintf('%.3F %.3F %.3F rg 18 814 559 8 re f', $blueBar[0], $blueBar[1], $blueBar[2]);
        $stream[] = sprintf('%.3F %.3F %.3F rg 18 744 559 68 re f', $blueSoft[0], $blueSoft[1], $blueSoft[2]);

        $stream[] = $this->pdfText(36, 785, 'KARTU UJI KOMPETENSI', 21, true, $headingColor);
        $stream[] = $this->pdfText(36, 765, $professionLabel, 9, false, $mutedColor);
        $stream[] = $this->pdfText(456, 784, 'TERVERIFIKASI', 9, true, $blueBar);
        $stream[] = $this->pdfLine(36, 748, 541, 748, $lineColor);

        $stream[] = sprintf('%.3F %.3F %.3F rg 40 596 88 104 re f', $blueSoft[0], $blueSoft[1], $blueSoft[2]);
        $stream[] = '0.78 0.84 0.93 RG 0.8 w 40 596 88 104 re S';
        if ($profilePhoto !== null) {
            $stream[] = $this->pdfImage('Im1', 40, 598, 88, 100);
        } else {
            $stream[] = $this->pdfText(57, 646, 'PAS FOTO', 12, true, $mutedColor);
            $stream[] = $this->pdfLine(52, 640, 114, 640, $lineColor);
        }

        $stream[] = $this->pdfSectionHeader(36, 720, 'DATA PESERTA', $headingColor, $lineColor);
        $stream[] = $this->pdfKeyValue(148, 688, 'NOMOR UJIAN', $examCard['exam_number'], 39, $headingColor, $textColor, 102, 116);
        $stream[] = $this->pdfKeyValue(148, 662, 'NAMA PESERTA', $user->name, 39, $headingColor, $textColor, 102, 116);
        $stream[] = $this->pdfKeyValue(148, 636, 'EMAIL', $user->email ?? '-', 39, $headingColor, $textColor, 102, 116);
        $stream[] = $this->pdfKeyValue(148, 610, 'NOMOR HP', $user->phone_number ?? '-', 39, $headingColor, $textColor, 102, 116);

        $stream[] = $this->pdfSectionHeader(36, 562, 'JADWAL UJI KOMPETENSI', $headingColor, $lineColor);
        $stream[] = $this->pdfKeyValue(42, 524, 'NAMA UJIAN', $examCard['exam']->name, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 500, 'HARI', $dayName, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 476, 'TANGGAL', $dateLabel, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 452, 'JAM', $timeLabel, 55, $headingColor, $textColor, 108, 124);

        $stream[] = $this->pdfSectionHeader(36, 414, 'LOKASI UJI KOMPETENSI', $headingColor, $lineColor);
        $stream[] = $this->pdfKeyValue(42, 376, 'PUSAT UJI', self::EXAM_CENTER, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 352, 'LOKASI', $locationLabel, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 328, 'RUANG', $roomLabel, 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 304, 'NOMOR KURSI', (string) $examCard['seat_number'], 55, $headingColor, $textColor, 108, 124);
        $stream[] = $this->pdfKeyValue(42, 280, 'ALAMAT', self::EXAM_ADDRESS, 58, $headingColor, $textColor, 108, 124);

        $stream[] = $this->pdfSectionHeader(36, 190, 'CATATAN PENTING', $headingColor, $lineColor);
        $stream[] = $this->pdfBullet(50, 158, 'Peserta wajib hadir 60 menit sebelum ujian dimulai.', $mutedColor);
        $stream[] = $this->pdfBullet(50, 140, 'Bawa kartu ujian ini dan identitas diri saat pelaksanaan ujian.', $mutedColor);
        $stream[] = $this->pdfBullet(50, 122, 'Status peserta: TERVERIFIKASI DAN TERJADWAL.', $mutedColor);

        return $this->wrapPdfDocument(implode("\n", array_filter($stream)), $profilePhoto);
    }

    private function wrapPdfDocument(string $content, ?array $image = null): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [5 0 R] /Count 1 >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
            5 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >>'
                . ($image ? ' /XObject << /Im1 7 0 R >>' : '')
                . ' >> /Contents 6 0 R >>',
            6 => '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
        ];

        if ($image !== null) {
            $objects[7] = '<< /Type /XObject /Subtype /Image /Width ' . $image['width']
                . ' /Height ' . $image['height']
                . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length '
                . strlen($image['data']) . " >>\nstream\n" . $image['data'] . "\nendstream";
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_keys($objects) as $id) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$id]) . "\n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function pdfKeyValue(
        int $x,
        int $y,
        string $label,
        string $value,
        int $maxChars = 48,
        array $labelColor = [0, 0, 0],
        array $valueColor = [0, 0, 0],
        int $colonX = 112,
        int $valueX = 126,
    ): string
    {
        $lines = $this->wrapPdfText($value, $maxChars);
        $colonX = $x + $colonX;
        $valueX = $x + $valueX;
        $chunks = [
            $this->pdfText($x, $y, $label, 10, true, $labelColor),
            $this->pdfText($colonX, $y, ':', 10, true, $labelColor),
        ];

        foreach ($lines as $index => $line) {
            $chunks[] = $this->pdfText($valueX, $y - ($index * 14), $line, 10, false, $valueColor);
        }

        return implode("\n", $chunks);
    }

    private function pdfBullet(int $x, int $y, string $text, array $textColor = [0, 0, 0]): string
    {
        return implode("\n", [
            $this->pdfText($x, $y, '- ', 10, true, $textColor),
            $this->pdfText($x + 14, $y, $text, 9, false, $textColor),
        ]);
    }

    private function pdfSectionHeader(int $x, int $y, string $title, array $titleColor, array $lineColor): string
    {
        return implode("\n", [
            $this->pdfText($x, $y, $title, 13, true, $titleColor),
            $this->pdfLine($x, $y - 8, 535, $y - 8, $lineColor),
        ]);
    }

    private function pdfLine(int $x1, int $y1, int $x2, int $y2, array $rgb = [0.75, 0.82, 0.95]): string
    {
        return sprintf('%.3F %.3F %.3F RG 1 w %d %d m %d %d l S', $rgb[0], $rgb[1], $rgb[2], $x1, $y1, $x2, $y2);
    }

    private function pdfText(int $x, int $y, string $text, int $size, bool $bold = false, array $rgb = [0, 0, 0]): string
    {
        $font = $bold ? 'F2' : 'F1';

        return sprintf(
            'BT /%s %d Tf %.3F %.3F %.3F rg 1 0 0 1 %d %d Tm (%s) Tj ET',
            $font,
            $size,
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $x,
            $y,
            $this->escapePdfText($text),
        );
    }

    private function pdfImage(string $imageName, int $x, int $y, int $width, int $height): string
    {
        return sprintf("q %d 0 0 %d %d %d cm /%s Do Q", $width, $height, $x, $y, $imageName);
    }

    private function expandExamDates(Exam $exam)
    {
        $dates = collect();
        $currentDate = $exam->start_date?->copy();
        $endDate = $exam->end_date?->copy() ?? $currentDate;

        while ($currentDate && $endDate && $currentDate->lte($endDate)) {
            $dates->push($currentDate->copy());
            $currentDate->addDay();
        }

        return $dates;
    }

    private function roomDisplayName(ExamRoom $room): string
    {
        return Str::startsWith(Str::lower($room->name), 'gedung')
            ? $room->name
            : 'Gedung GSC R. ' . $room->name;
    }

    private function locationLabel(ExamRoom $room): string
    {
        return self::EXAM_LOCATION . ' - ' . $room->name;
    }

    private function wrapPdfText(string $text, int $maxChars = 48): array
    {
        return preg_split("/\r\n|\n|\r/", wordwrap(trim($text) ?: '-', $maxChars, "\n", true)) ?: ['-'];
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\(', '\)', '', ' '],
            $text,
        );
    }

    private function resolveProfilePhotoForPdf(User $user): ?array
    {
        $photoPath = null;

        $pasFoto = $user->documents()
            ->where('document_type', 'pas_foto')
            ->where('mime_type', 'like', 'image/%')
            ->latest('uploaded_at')
            ->first();

        if ($pasFoto && Storage::disk('public')->exists($pasFoto->file_path)) {
            $photoPath = Storage::disk('public')->path($pasFoto->file_path);
        } elseif ($user->profile_photo) {
            $profilePath = public_path(ltrim($user->profile_photo, '/'));

            if (is_file($profilePath)) {
                $photoPath = $profilePath;
            }
        }

        if (! $photoPath || ! is_file($photoPath)) {
            return null;
        }

        return $this->preparePdfImage($photoPath);
    }

    private function preparePdfImage(string $path): ?array
    {
        $imageInfo = @getimagesize($path);

        if (! $imageInfo) {
            return null;
        }

        [$width, $height, $type] = $imageInfo;

        if ($type === IMAGETYPE_JPEG) {
            $data = @file_get_contents($path);

            return $data === false ? null : [
                'width' => $width,
                'height' => $height,
                'data' => $data,
            ];
        }

        if (! function_exists('imagecreatefrompng') || ! function_exists('imagejpeg')) {
            return null;
        }

        $source = match ($type) {
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $source) {
            return null;
        }

        $canvas = imagecreatetruecolor(imagesx($source), imagesy($source));
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));

        ob_start();
        imagejpeg($canvas, null, 90);
        $data = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($source);

        return $data === false ? null : [
            'width' => $width,
            'height' => $height,
            'data' => $data,
        ];
    }

}
