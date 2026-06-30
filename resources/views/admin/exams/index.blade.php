@extends('admin.layout.app')

@section('title', 'CRUD Ujian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="dash-content admin-crud">
        <div class="dash-header">
            <div>
                <h1>Ujian</h1>
                <p>Kelola range tanggal, sesi, dan pilihan ruangan untuk ujian penerimaan.</p>
            </div>
            <div class="dash-header-actions">
                <a href="{{ route('admin.exams.create') }}" class="btn-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Ujian
                </a>
            </div>
        </div>

        <section class="crud-card">
            <div class="crud-table-wrap">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Nama Ujian</th>
                            <th>Range Tanggal</th>
                            <th>Sesi</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td>
                                    <strong>{{ $exam->name }}</strong>
                                    <span>Dibuat {{ $exam->created_at?->diffForHumans() }}</span>
                                </td>
                                <td>{{ $exam->start_date?->format('d/m/Y') }} - {{ $exam->end_date?->format('d/m/Y') }}</td>
                                <td>
                                    @foreach($exam->sessions as $session)
                                        <div>{{ $session->order }}. {{ $session->start_time?->format('H:i') }} - {{ $session->end_time?->format('H:i') }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    <strong>{{ $exam->rooms->pluck('name')->join(', ') ?: '-' }}</strong>
                                    <span>{{ number_format($exam->rooms->sum('capacity')) }} total kursi</span>
                                </td>
                                <td>
                                    <div class="crud-actions">
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="crud-icon-btn" title="Edit ujian">
                                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Hapus ujian {{ addslashes($exam->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="crud-icon-btn danger" title="Hapus ujian">
                                                <svg viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6l-1 14H6L5 6"/><path d="M8 6V4h8v2"/><path d="M10 11v6M14 11v6"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="crud-empty">Belum ada data ujian penerimaan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="crud-pagination">
                {{ $exams->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
