<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use Illuminate\Http\JsonResponse;

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
                'has_foto'           => (bool) $pendaftar->foto,
                'initials'           => $pendaftar->initials,
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

}
