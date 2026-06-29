<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use App\Support\ProfessionRequirements;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserDashboardController extends Controller
{
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
            'format' => 'PDF/JPG/PNG',
            'mimes' => 'pdf,jpg,jpeg,png',
            'tone' => 'olive',
            'icon' => 'camera',
            'wide' => true,
        ],
    ];

    public function index(): View
    {
        $user = Auth::user();
        $specialRequirement = ProfessionRequirements::for($user->profesi);
        $uploadedDocuments = $user
            ->documents()
            ->get()
            ->groupBy('document_type');

        return view('user.dashboard', [
            'documents' => self::DOCUMENTS,
            'specialRequirement' => $specialRequirement,
            'specialDocuments' => $specialRequirement['documents'] ?? [],
            'uploadedDocuments' => $uploadedDocuments,
            'documentsSubmitted' => $user->documents_submitted_at !== null,
        ]);
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
}
