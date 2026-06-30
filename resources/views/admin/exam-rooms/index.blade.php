@extends('admin.layout.app')

@section('title', 'CRUD Ruangan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="dash-content admin-crud">
        <div class="dash-header">
            <div>
                <h1>Ruangan</h1>
                <p>Kelola nama ruangan CBT dan kapasitas kursi untuk kebutuhan ujian penerimaan.</p>
            </div>
            <div class="dash-header-actions">
                <a href="{{ route('admin.exam-rooms.create') }}" class="btn-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Ruangan
                </a>
            </div>
        </div>

        <section class="crud-card">
            <div class="crud-table-wrap">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Nama Ruangan</th>
                            <th>Kapasitas</th>
                            <th>Dipakai Ujian</th>
                            <th>Terakhir Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>
                                    <strong>{{ $room->name }}</strong>
                                    <span>Ruang Computer Based Test</span>
                                </td>
                                <td>{{ number_format($room->capacity) }} kursi</td>
                                <td>{{ number_format($room->exams_count) }} ujian</td>
                                <td>{{ $room->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <div class="crud-actions">
                                        <a href="{{ route('admin.exam-rooms.edit', $room) }}" class="crud-icon-btn" title="Edit ruangan">
                                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.exam-rooms.destroy', $room) }}" onsubmit="return confirm('Hapus ruangan {{ addslashes($room->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="crud-icon-btn danger" title="Hapus ruangan">
                                                <svg viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6l-1 14H6L5 6"/><path d="M8 6V4h8v2"/><path d="M10 11v6M14 11v6"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="crud-empty">Belum ada ruangan. Tambahkan CBT 1, CBT 2, atau ruangan baru lainnya.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="crud-pagination">
                {{ $rooms->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
