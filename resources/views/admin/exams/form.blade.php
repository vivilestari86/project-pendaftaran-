@extends('admin.layout.app')

@section('title', $exam->exists ? 'Edit Ujian' : 'Tambah Ujian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endpush

@section('content')
@php
    $selectedSessionCount = (int) old('session_count', $exam->exists ? max($exam->sessions()->count(), 1) : 1);
    $selectedSessionCount = min(max($selectedSessionCount, 1), 3);
    $selectedRoomIds = collect(old('exam_room_ids', $exam->exists ? $exam->rooms->pluck('id')->all() : []))
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp

<div class="dash-wrapper">
    <div class="dash-content admin-crud">
        <div class="crud-breadcrumb">
            <a href="{{ route('admin.exams.index') }}">Ujian</a>
            <span>&gt;</span>
            <span>{{ $exam->exists ? 'Edit' : 'Tambah' }}</span>
        </div>

        <div class="dash-header">
            <div>
                <h1>{{ $exam->exists ? 'Edit Ujian' : 'Tambah Ujian' }}</h1>
                <p>Isi nama ujian, range tanggal, sesi, dan ruangan yang akan digunakan.</p>
            </div>
        </div>

        <section class="crud-form-card exam-form-card">
            @if($rooms->isEmpty())
                <div class="crud-empty">
                    Belum ada ruangan ujian. Tambahkan ruangan terlebih dahulu sebelum membuat ujian.
                </div>
            @else
                <form method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam) : route('admin.exams.store') }}">
                    @csrf
                    @if($exam->exists)
                        @method('PUT')
                    @endif

                    <div class="crud-field span-2">
                        <label for="name">Nama Ujian</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $exam->name ?: 'Nama Ujian') }}" placeholder="Nama Ujian" required>
                        @error('name')<span class="crud-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-form-grid">
                        <div class="crud-field">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $exam->start_date?->format('Y-m-d')) }}" required>
                            @error('start_date')<span class="crud-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="crud-field">
                            <label for="end_date">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $exam->end_date?->format('Y-m-d')) }}" required>
                            @error('end_date')<span class="crud-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="crud-field span-2">
                        <label for="session_count">Jumlah Sesi</label>
                        <input type="number" id="session_count" name="session_count" value="{{ $selectedSessionCount }}" min="1" max="3" required>
                        @error('session_count')<span class="crud-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field span-2">
                        <label>Jam Sesi</label>
                        @error('sessions')<span class="crud-error">{{ $message }}</span>@enderror
                        <div class="session-grid" data-session-grid>
                            @foreach($sessionRows as $index => $session)
                                <div class="session-row" data-session-row="{{ $index + 1 }}">
                                    <div class="session-number">{{ $index + 1 }}</div>
                                    <input type="time" name="sessions[{{ $index }}][start_time]" value="{{ old("sessions.$index.start_time", $session['start_time']) }}">
                                    <span>-</span>
                                    <input type="time" name="sessions[{{ $index }}][end_time]" value="{{ old("sessions.$index.end_time", $session['end_time']) }}">
                                </div>
                                @error("sessions.$index.start_time")<span class="crud-error">{{ $message }}</span>@enderror
                                @error("sessions.$index.end_time")<span class="crud-error">{{ $message }}</span>@enderror
                            @endforeach
                        </div>
                    </div>

                    <div class="crud-field span-2">
                        <label for="exam_room_ids">Pilih Ruangan</label>
                        <select id="exam_room_ids" name="exam_room_ids[]" multiple required size="6" class="multi-select">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ in_array((string) $room->id, $selectedRoomIds, true) ? 'selected' : '' }}>
                                    {{ $room->name }} - {{ number_format($room->capacity) }} kursi
                                </option>
                            @endforeach
                        </select>
                        <small class="crud-help">Tahan Ctrl untuk memilih beberapa ruangan.</small>
                        @error('exam_room_ids')<span class="crud-error">{{ $message }}</span>@enderror
                        @error('exam_room_ids.*')<span class="crud-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-form-actions">
                        <a href="{{ route('admin.exams.index') }}" class="btn-outline">Batal</a>
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                            Simpan Ujian
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sessionCount = document.getElementById('session_count');
        const sessionRows = document.querySelectorAll('[data-session-row]');

        if (!sessionCount || sessionRows.length === 0) {
            return;
        }

        const syncSessionRows = () => {
            const count = Math.min(Math.max(parseInt(sessionCount.value || '1', 10), 1), 3);
            sessionCount.value = count;

            sessionRows.forEach((row) => {
                const rowNumber = parseInt(row.dataset.sessionRow, 10);
                const isActive = rowNumber <= count;

                row.classList.toggle('is-hidden', !isActive);
                row.querySelectorAll('input').forEach((input) => {
                    input.required = isActive;
                    input.disabled = !isActive;
                });
            });
        };

        sessionCount.addEventListener('input', syncSessionRows);
        sessionCount.addEventListener('change', syncSessionRows);
        syncSessionRows();
    });
</script>
@endpush
