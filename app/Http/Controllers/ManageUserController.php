<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDocument;
use App\Support\ProfessionRequirements;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManageUserController extends Controller
{
    private const REQUIRED_DOCUMENTS = [
        'surat_lamaran' => 'Surat Lamaran',
        'cv_resume' => 'CV / Resume',
        'ijazah' => 'Scan Ijazah',
        'transkrip' => 'Transkrip Nilai',
        'ktp' => 'KTP',
        'surat_keterangan_sehat' => 'Surat Keterangan Sehat',
        'pas_foto' => 'Pas Foto',
    ];

    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $completeUserIds = $this->completeUserIds();
        $verifiedUserIds = $this->verifiedUserIds();

        $users = User::query()
            ->with('documents')
            ->where('role', 'user')
            ->when($search, fn($q) => $q->where(fn($inner) =>
                $inner->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest('last_active_at')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total'    => User::where('role', 'user')->count(),
            'active'   => User::where('role', 'user')->where('status', 'Active')->count(),
            'admins'   => User::where('role', 'admin')->count(),
            'inactive' => User::where('role', 'user')->where('status', 'Inactive')->count(),
        ];

        return view('admin.manage-user.index', compact('users', 'stats', 'search', 'perPage', 'completeUserIds', 'verifiedUserIds'));
    }

    // Route model binding pakai 'manageUser' agar tidak bentrok dengan {user} milik auth
    public function edit(User $manageUser)
    {
        $manageUser->load('documents');
        $generalDocuments = self::REQUIRED_DOCUMENTS;
        $specialDocuments = ProfessionRequirements::labelsFor($manageUser->profesi);
        $requiredDocuments = array_merge($generalDocuments, $specialDocuments);

        return view('admin.manage-user.edit', [
            'user' => $manageUser,
            'requiredDocumentCount' => count($requiredDocuments),
            'uploadedDocumentCount' => $this->uploadedDocumentCount($manageUser),
            'isDocumentComplete' => $this->isDocumentComplete($manageUser),
            'isDocumentVerified' => $this->isDocumentVerified($manageUser),
            'profilePhotoUrl' => $this->profilePhotoUrl($manageUser),
            'documentGroups' => [
                [
                    'title' => 'Persyaratan Umum',
                    'description' => 'Dokumen utama yang wajib dicek untuk semua pendaftar.',
                    'categories' => $this->documentCategories($manageUser, $generalDocuments),
                ],
                [
                    'title' => 'Persyaratan Khusus',
                    'description' => $specialDocuments === []
                        ? 'Tidak ada persyaratan khusus untuk profesi ini.'
                        : 'Dokumen tambahan sesuai profesi yang dipilih pendaftar.',
                    'categories' => $this->documentCategories($manageUser, $specialDocuments),
                ],
            ],
        ]);
    }

    public function update(Request $request, User $manageUser)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $manageUser->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.manage-users.edit', $manageUser)
            ->with('success', 'Password akun berhasil diperbarui.');
    }

    public function verify(User $manageUser)
    {
        if (! $this->isDocumentComplete($manageUser)) {
            return redirect()->route('admin.manage-users.edit', $manageUser)
                ->with('error', 'Dokumen belum lengkap. Verifikasi hanya bisa dilakukan setelah semua dokumen wajib diupload.');
        }

        $manageUser->documents()
            ->whereIn('document_type', array_keys($this->requiredDocumentsFor($manageUser)))
            ->update(['status' => 'verified']);

        return redirect()->route('admin.manage-users.edit', $manageUser)
            ->with('success', 'Dokumen peserta berhasil diverifikasi.');
    }

    public function destroy(User $manageUser)
    {
        if ($manageUser->is(auth()->user())) {
            return redirect()->route('admin.manage-users.index')
                ->with('error', 'Anda tidak bisa menghapus akun yang sedang digunakan.');
        }

        if ($manageUser->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.manage-users.index')
                ->with('error', 'Minimal harus ada satu akun admin aktif.');
        }

        $manageUser->delete();

        return redirect()->route('admin.manage-users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function showDocument(User $manageUser, UserDocument $document): BinaryFileResponse
    {
        abort_unless($document->user_id === $manageUser->id, 404);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($document->file_path),
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_name) . '"',
            ],
        );
    }

    private function completeUserIds()
    {
        return User::query()
            ->where('role', 'user')
            ->with('documents')
            ->get()
            ->filter(fn (User $user): bool => $this->isDocumentComplete($user))
            ->pluck('id');
    }

    private function verifiedUserIds()
    {
        return User::query()
            ->where('role', 'user')
            ->with('documents')
            ->get()
            ->filter(fn (User $user): bool => $this->isDocumentVerified($user))
            ->pluck('id');
    }

    private function uploadedDocumentCount(User $user): int
    {
        $requiredDocumentTypes = array_keys($this->requiredDocumentsFor($user));

        if ($user->relationLoaded('documents')) {
            return $user->documents
                ->whereIn('document_type', $requiredDocumentTypes)
                ->pluck('document_type')
                ->unique()
                ->count();
        }

        return $user->documents()
            ->whereIn('document_type', $requiredDocumentTypes)
            ->distinct('document_type')
            ->count('document_type');
    }

    private function isDocumentComplete(User $user): bool
    {
        return $this->uploadedDocumentCount($user) >= count($this->requiredDocumentsFor($user));
    }

    private function isDocumentVerified(User $user): bool
    {
        $requiredDocumentTypes = array_keys($this->requiredDocumentsFor($user));

        if ($user->relationLoaded('documents')) {
            return $user->documents
                ->whereIn('document_type', $requiredDocumentTypes)
                ->where('status', 'verified')
                ->pluck('document_type')
                ->unique()
                ->count() >= count($requiredDocumentTypes);
        }

        return $user->documents()
            ->whereIn('document_type', $requiredDocumentTypes)
            ->where('status', 'verified')
            ->distinct('document_type')
            ->count('document_type') >= count($requiredDocumentTypes);
    }

    private function requiredDocumentsFor(User $user): array
    {
        return array_merge(self::REQUIRED_DOCUMENTS, ProfessionRequirements::labelsFor($user->profesi));
    }

    private function documentCategories(User $user, array $documents): Collection
    {
        return collect($documents)->map(function (string $label, string $type) use ($user) {
            return [
                'type' => $type,
                'label' => $label,
                'documents' => $user->documents
                    ->where('document_type', $type)
                    ->sortBy('document_slot')
                    ->values(),
            ];
        });
    }

    private function profilePhotoUrl(User $user): ?string
    {
        $pasFoto = $user->documents
            ->where('document_type', 'pas_foto')
            ->filter(fn (UserDocument $document) => str_starts_with((string) $document->mime_type, 'image/'))
            ->sortByDesc('uploaded_at')
            ->first();

        if ($pasFoto && Storage::disk('public')->exists($pasFoto->file_path)) {
            return route('admin.manage-users.documents.show', [$user, $pasFoto]);
        }

        return $user->profile_photo_url;
    }
}
