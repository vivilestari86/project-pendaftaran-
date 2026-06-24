{{--
    resources/views/admin/dasboard/index.blade.php
    Dashboard Admin - Ringkasan Pendaftaran
    View ini mengimpor berbagai partials untuk modularitas
--}}

@extends('admin.layout.app')

@section('title', 'Ringkasan Pendaftaran')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                <button type="button" class="btn-outline">📅 7 Hari Terakhir</button>
                <button type="button" class="btn-primary">⬇ Laporan Data</button>
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
