@extends('admin.layout.app')

@section('title', $room->exists ? 'Edit Ruangan' : 'Tambah Ruangan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="dash-content admin-crud">
        <div class="crud-breadcrumb">
            <a href="{{ route('admin.exam-rooms.index') }}">Ruangan</a>
            <span>&gt;</span>
            <span>{{ $room->exists ? 'Edit' : 'Tambah' }}</span>
        </div>

        <div class="dash-header">
            <div>
                <h1>{{ $room->exists ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h1>
                <p>Nama ruangan dan kapasitas dapat diubah sesuai kebutuhan operasional CBT.</p>
            </div>
        </div>

        <section class="crud-form-card">
            <form method="POST" action="{{ $room->exists ? route('admin.exam-rooms.update', $room) : route('admin.exam-rooms.store') }}">
                @csrf
                @if($room->exists)
                    @method('PUT')
                @endif

                <div class="crud-form-grid">
                    <div class="crud-field">
                        <label for="name">Nama Ruangan</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" placeholder="Contoh: CBT 1" required>
                        @error('name')<span class="crud-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="crud-field">
                        <label for="capacity">Kapasitas Kursi</label>
                        <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity ?: 20) }}" min="1" required>
                        @error('capacity')<span class="crud-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route('admin.exam-rooms.index') }}" class="btn-outline">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
