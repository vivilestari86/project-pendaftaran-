{{-- resources/views/admin/dasboard/modals/modal-detail.blade.php --}}

<div class="modal-overlay" id="modalDetailPendaftar">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Detail Pendaftar</h3>
            <button type="button" class="modal-close" aria-label="Tutup">
                &times;
            </button>
        </div>

        <div class="modal-body" id="modalDetailBody">
            <div class="modal-loading">Memuat data...</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-outline modal-close" style="flex:1;">
                Tutup
            </button>
            <button type="button" class="btn-primary modal-edit-btn" style="flex:1;">
                Edit Data
            </button>
        </div>
    </div>
</div>