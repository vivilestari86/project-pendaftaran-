<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    private const REQUIRED_DOCUMENTS = [
        'surat_lamaran',
        'cv_resume',
        'ijazah',
        'transkrip',
        'ktp',
        'surat_keterangan_sehat',
        'pas_foto',
    ];

    /**
     * Tampilkan halaman dashboard admin
     */
    public function index()
    {
        $completeUserIds = $this->completeUserIds();
        $userQuery = User::query()->where('role', 'user');

        // Kartu Statistik
        $totalPendaftar    = (clone $userQuery)->count();
        $totalLengkap      = (clone $userQuery)->whereIn('id', $completeUserIds)->count();
        $totalBelumLengkap = max($totalPendaftar - $totalLengkap, 0);
        $pendaftarHariIni  = (clone $userQuery)->whereDate('created_at', now()->toDateString())->count();
        $targetHarian      = 200;
        $tingkatKelengkapan = $totalPendaftar > 0
            ? round(($totalLengkap / $totalPendaftar) * 100, 1)
            : 0;

        // Grafik Tren Mingguan (7 hari: Sen - Min)
        $labelHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $trenMingguan = [];
        $startOfWeek = now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $tanggal = $startOfWeek->copy()->addDays($i);
            $trenMingguan[] = [
                'label'       => $labelHari[$i],
                'tanggal'     => $tanggal->toDateString(),
                'pendaftar'   => (clone $userQuery)->whereDate('created_at', $tanggal)->count(),
                'kelengkapan' => (clone $userQuery)
                    ->whereIn('id', $completeUserIds)
                    ->whereDate('created_at', $tanggal)
                    ->count(),
            ];
        }

        // Pendaftar Terbaru (4 item)
        $pendaftarTerbaru = $this->mapUsersForDashboard(
            (clone $userQuery)->with('documents')->latest('created_at')->take(4)->get(),
            $completeUserIds,
        );
        $semuaPendaftar = $this->mapUsersForDashboard(
            (clone $userQuery)->with('documents')->latest('created_at')->take(12)->get(),
            $completeUserIds,
        );

        // Distribusi Profesi
        $distribusiWilayah = (clone $userQuery)->get(['profesi'])
            ->groupBy(fn (User $user) => $user->profesi ?: 'Belum Diisi')
            ->map(function ($items, string $profesi) use ($totalPendaftar) {
                return [
                    'wilayah'    => $profesi,
                    'total'      => $items->count(),
                    'persentase' => $totalPendaftar > 0 ? round(($items->count() / $totalPendaftar) * 100) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Kapasitas Pendaftaran
        $kuotaTotal = 24252;
        $persentaseKuota = $kuotaTotal > 0
            ? round(($totalPendaftar / $kuotaTotal) * 100, 1)
            : 0;

        return view('admin.dashboard.index', [
            'totalLengkap'        => $totalLengkap,
            'totalBelumLengkap'   => $totalBelumLengkap,
            'pendaftarHariIni'    => $pendaftarHariIni,
            'targetHarian'        => $targetHarian,
            'tingkatKelengkapan'  => $tingkatKelengkapan,
            'trenMingguan'        => $trenMingguan,
            'pendaftarTerbaru'    => $pendaftarTerbaru,
            'semuaPendaftar'      => $semuaPendaftar,
            'distribusiWilayah'   => $distribusiWilayah,
            'kuotaTotal'          => $kuotaTotal,
            'totalPendaftar'      => $totalPendaftar,
            'persentaseKuota'     => $persentaseKuota,
        ]);
    }

    public function exportExcel()
    {
        $completeUserIds = $this->completeUserIds();
        $pendaftars = $this->mapUsersForDashboard(
            User::where('role', 'user')->with('documents')->latest('created_at')->get(),
            $completeUserIds,
        );

        $filename = 'data-pendaftaran-' . now()->format('Y-m-d_H-i-s') . '.xls';

        return response()
            ->view('admin.dashboard.export.pendaftars-excel', [
                'pendaftars' => $pendaftars,
                'exportedAt' => now(),
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Ambil detail pendaftar untuk modal VIEW
     * Route: GET /admin/pendaftar/{pendaftar}/detail
     */
    public function detail(User $pendaftar): JsonResponse
    {
        abort_unless($pendaftar->isUser(), 404);

        $uploadedCount = $pendaftar->documents()
            ->whereIn('document_type', self::REQUIRED_DOCUMENTS)
            ->distinct('document_type')
            ->count('document_type');
        $isComplete = $uploadedCount >= count(self::REQUIRED_DOCUMENTS);

        return response()->json([
            'success' => true,
            'data' => [
                'id'                 => $pendaftar->id,
                'nama'               => $pendaftar->name,
                'id_pendaftar'       => 'USR-' . str_pad((string) $pendaftar->id, 6, '0', STR_PAD_LEFT),
                'profesi'            => $pendaftar->profesi ?: '-',
                'foto'               => $pendaftar->profile_photo_url,
                'has_foto'           => (bool) $pendaftar->profile_photo_url,
                'initials'           => $this->initials($pendaftar->name),
                'wilayah'            => 'Dokumen: ' . $uploadedCount . '/' . count(self::REQUIRED_DOCUMENTS),
                'status_kelengkapan' => $isComplete ? 'LENGKAP' : 'BELUM LENGKAP',
                'badge_color'        => $isComplete ? 'success' : 'warning',
                'email'              => $pendaftar->email ?? '-',
                'no_hp'              => $pendaftar->phone_number ?? '-',
                'alamat'             => '-',
                'catatan'            => '-',
                'tanggal_daftar'     => $pendaftar->created_at?->translatedFormat('d F Y, H:i') ?? '-',
                'waktu_relatif'      => $pendaftar->created_at?->diffForHumans() ?? '-',
            ],
        ]);
    }

    private function completeUserIds()
    {
        return UserDocument::query()
            ->select('user_id')
            ->whereIn('document_type', self::REQUIRED_DOCUMENTS)
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT document_type) >= ?', [count(self::REQUIRED_DOCUMENTS)])
            ->pluck('user_id');
    }

    private function mapUsersForDashboard($users, $completeUserIds)
    {
        return $users->map(function (User $user) use ($completeUserIds) {
            $isComplete = $completeUserIds->contains($user->id);
            $uploadedCount = $user->documents
                ->whereIn('document_type', self::REQUIRED_DOCUMENTS)
                ->pluck('document_type')
                ->unique()
                ->count();

            return (object) [
                'id' => $user->id,
                'nama' => $user->name,
                'id_pendaftar' => 'USR-' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                'profesi' => $user->profesi ?: '-',
                'email' => $user->email,
                'no_hp' => $user->phone_number,
                'wilayah' => $uploadedCount . '/' . count(self::REQUIRED_DOCUMENTS) . ' dokumen',
                'badge_color' => $isComplete ? 'success' : 'warning',
                'status_label' => $isComplete ? 'LENGKAP' : 'BELUM LENGKAP',
                'tanggal_daftar' => $user->created_at,
                'waktu_relatif' => $user->created_at?->diffForHumans() ?? '-',
            ];
        });
    }

    private function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $initials = collect($words)
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return $initials ?: 'U';
    }
}
