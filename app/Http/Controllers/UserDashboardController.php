<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $uploadedDocuments = Auth::user()
            ->documents()
            ->get()
            ->groupBy('document_type');

        return view('user.dashboard', [
            'documents' => self::DOCUMENTS,
            'uploadedDocuments' => $uploadedDocuments,
        ]);
    }

    public function storeDocuments(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (self::DOCUMENTS as $key => $document) {
            if ($document['multiple'] ?? false) {
                $rules["documents.$key"] = 'nullable|array|max:' . $document['max_files'];
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

        $validator->after(function (Validator $validator) use ($request): void {
            $hasUploadedFile = collect(array_keys(self::DOCUMENTS))
                ->contains(fn (string $key): bool => $request->hasFile("documents.$key"));

            if (! $hasUploadedFile) {
                $validator->errors()->add('documents', 'Pilih minimal satu dokumen untuk diupload.');
            }
        });

        $validator->validate();

        $user = Auth::user();
        $savedCount = 0;

        foreach (self::DOCUMENTS as $key => $document) {
            if (! $request->hasFile("documents.$key")) {
                continue;
            }

            $files = $document['multiple'] ?? false
                ? $request->file("documents.$key", [])
                : [$request->file("documents.$key")];

            UserDocument::where('user_id', $user->id)
                ->where('document_type', $key)
                ->get()
                ->each(function (UserDocument $existingDocument): void {
                    Storage::disk('public')->delete($existingDocument->file_path);
                    $existingDocument->delete();
                });

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

    public function showDocument(UserDocument $document): BinaryFileResponse
    {
        abort_unless($document->user_id === Auth::id(), 403);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($document->file_path),
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_name) . '"',
            ],
        );
    }
}
