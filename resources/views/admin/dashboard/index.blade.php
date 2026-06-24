{{--
    resources/views/admin/dasboard/index.blade.php
    Dashboard Admin - Ringkasan Pendaftaran
    View ini mengimpor berbagai partials untuk modularitas
--}}

@extends('admin.layout.app')

@section('title', 'Ringkasan Pendaftaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="dash-content">

        {{-- ============ HEADER ============ --}}
        <div class="dash-header">
            <div>
                <h1>Ringkasan Pendaftaran</h1>
                <p>Monitor data pendaftar dan status kelengkapan secara real-time.</p>
            </div>
            <div class="dash-header-actions">
                <button type="button" class="btn-outline">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    7 Hari Terakhir
                </button>
                <button type="button" class="btn-primary">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                    Ekspor Data
                </button>
            </div>
        </div>

        {{-- ============ KARTU STATISTIK ============ --}}
        @include('admin.dashboard.partials.stat-cards')

        {{-- ============ GRID TENGAH ============ --}}
        <div class="dash-grid-main">
            @include('admin.dashboard.partials.chart-trend')
            @include('admin.dashboard.partials.pendaftar-list')
        </div>

        {{-- ============ GRID BAWAH ============ --}}
        <div class="dash-grid-bottom">
            @include('admin.dashboard.partials.distribusi-wilayah')
            @include('admin.dashboard.partials.kapasitas-pendaftaran')
        </div>

    </div>
</div>

{{-- ============ MODALS ============ --}}
@include('admin.dashboard.modals.modal-detail')
@include('admin.dashboard.modals.modal-edit')

@endsection

{{-- ============ SCRIPTS ============ --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

<script>
    // Pass data ke JavaScript
    window.trenMingguan = @json($trenMingguan);
</script>

<script src="{{ asset('js/admin/dashboard.js') }}"></script>
@endpush
