<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pendaftaran</title>
</head>
<body>
    <table border="1">
        <tr>
            <th colspan="9" style="font-size:16px;">Data Pendaftaran</th>
        </tr>
        <tr>
            <td colspan="9">Diekspor pada: {{ $exportedAt->format('d-m-Y H:i:s') }}</td>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>ID Pendaftar</th>
            <th>Profesi</th>
            <th>Email</th>
            <th>No HP</th>
            <th>Wilayah</th>
            <th>Status Kelengkapan</th>
            <th>Tanggal Daftar</th>
        </tr>
        @forelse ($pendaftars as $index => $pendaftar)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pendaftar->nama }}</td>
                <td>{{ $pendaftar->id_pendaftar }}</td>
                <td>{{ $pendaftar->profesi ?: '-' }}</td>
                <td>{{ $pendaftar->email ?: '-' }}</td>
                <td>{{ $pendaftar->no_hp ?: '-' }}</td>
                <td>{{ $pendaftar->wilayah }}</td>
                <td>{{ $pendaftar->status_label }}</td>
                <td>{{ $pendaftar->tanggal_daftar?->format('d-m-Y H:i') ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">Belum ada data pendaftar.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
