<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin
     */
    public function index()
    {
        // Kartu Statistik
        $totalLengkap      = Pendaftar::lengkap()->count();
        $totalBelumLengkap = Pendaftar::belumLengkap()->count();
        $pendaftarHariIni  = Pendaftar::hariIni()->count();
        $totalPendaftar    = Pendaftar::count();
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
                'pendaftar'   => Pendaftar::whereDate('tanggal_daftar', $tanggal)->count(),
                'kelengkapan' => Pendaftar::whereDate('tanggal_daftar', $tanggal)->lengkap()->count(),
            ];
        }

        // Pendaftar Terbaru (4 item)
        $pendaftarTerbaru = Pendaftar::latest('tanggal_daftar')->take(4)->get();
        $semuaPendaftar = Pendaftar::latest('tanggal_daftar')->take(12)->get();

        // Distribusi Wilayah
        $distribusiWilayah = Pendaftar::selectRaw('wilayah, COUNT(*) as total')
            ->groupBy('wilayah')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalPendaftar) {
                return [
                    'wilayah'    => $item->wilayah,
                    'total'      => $item->total,
                    'persentase' => $totalPendaftar > 0 ? round(($item->total / $totalPendaftar) * 100) : 0,
                ];
            });

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

    public function profile()
    {
        return view('admin.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'phone_number' => ['required', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'phone_number.required' => 'Nomor HP wajib diisi.',
            'profile_photo.image' => 'File foto harus berupa gambar.',
            'profile_photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('profile_photo')) {
            $directory = public_path('uploads/profile-photos');

            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            if ($user->profile_photo) {
                $oldPath = public_path($user->profile_photo);

                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $extension = $request->file('profile_photo')->getClientOriginalExtension();
            $filename = 'profile-' . $user->id . '-' . now()->format('YmdHis') . '.' . $extension;
            $request->file('profile_photo')->move($directory, $filename);
            $data['profile_photo'] = 'uploads/profile-photos/' . $filename;
        }

        $user->update([
            'phone_number' => $data['phone_number'],
            'profile_photo' => $data['profile_photo'] ?? $user->profile_photo,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function settings()
    {
        return view('admin.settings.index', [
            'user' => Auth::user(),
        ]);
    }

    public function exportExcel()
    {
        $pendaftars = Pendaftar::latest('tanggal_daftar')->get();

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
    public function detail(Pendaftar $pendaftar): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id'                 => $pendaftar->id,
                'nama'               => $pendaftar->nama,
                'id_pendaftar'       => $pendaftar->id_pendaftar,
                'profesi'            => $pendaftar->profesi,
                'foto'               => $pendaftar->foto_url,
                'wilayah'            => $pendaftar->wilayah,
                'status_kelengkapan' => $pendaftar->status_label,
                'badge_color'        => $pendaftar->badge_color,
                'email'              => $pendaftar->email ?? '-',
                'no_hp'              => $pendaftar->no_hp ?? '-',
                'alamat'             => $pendaftar->alamat ?? '-',
                'catatan'            => $pendaftar->catatan ?? '-',
                'tanggal_daftar'     => $pendaftar->tanggal_daftar?->translatedFormat('d F Y, H:i') ?? '-',
                'waktu_relatif'      => $pendaftar->waktu_relatif,
            ],
        ]);
    }

    /**
     * Ambil data pendaftar untuk modal EDIT/UPDATE
     * Route: GET /admin/pendaftar/{pendaftar}/edit-form
     */
    public function editForm(Pendaftar $pendaftar): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id'                 => $pendaftar->id,
                'nama'               => $pendaftar->nama,
                'id_pendaftar'       => $pendaftar->id_pendaftar,
                'profesi'            => $pendaftar->profesi,
                'email'              => $pendaftar->email,
                'no_hp'              => $pendaftar->no_hp,
                'alamat'             => $pendaftar->alamat,
                'wilayah'            => $pendaftar->wilayah,
                'status_kelengkapan' => $pendaftar->status_kelengkapan,
                'catatan'            => $pendaftar->catatan,
            ],
        ]);
    }

    /**
     * Update data pendaftar
     * Route: PUT /admin/pendaftar/{pendaftar}/update
     */
    public function update(Request $request, Pendaftar $pendaftar): JsonResponse
    {
        $validated = $request->validate([
            'nama'               => 'required|string|max:100',
            'profesi'            => 'nullable|string|max:100',
            'email'              => 'nullable|email|max:100|unique:pendaftars,email,' . $pendaftar->id,
            'no_hp'              => 'nullable|string|max:20',
            'alamat'             => 'nullable|string|max:500',
            'wilayah'            => 'required|in:Jawa Barat,Jawa Timur,Jawa Tengah,Lainnya',
            'status_kelengkapan' => 'required|in:lengkap,belum_lengkap',
            'catatan'            => 'nullable|string|max:1000',
        ]);

        try {
            $pendaftar->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data pendaftar berhasil diperbarui',
                'data' => [
                    'id'                 => $pendaftar->id,
                    'nama'               => $pendaftar->nama,
                    'id_pendaftar'       => $pendaftar->id_pendaftar,
                    'profesi'            => $pendaftar->profesi,
                    'status_kelengkapan' => $pendaftar->status_label,
                    'badge_color'        => $pendaftar->badge_color,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
