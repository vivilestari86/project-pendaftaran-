{{-- resources/views/admin/dasboard/modals/modal-edit.blade.php --}}

<div class="modal-overlay" id="modalEditPendaftar">
    <div class="modal-box modal-box-lg">
        <div class="modal-head">
            <h3>Edit Data Pendaftar</h3>
            <button type="button" class="modal-close" aria-label="Tutup">
                &times;
            </button>
        </div>

        <div class="modal-body" id="modalEditBody">
            <div class="modal-loading">Memuat form...</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-outline modal-close" style="flex:1;">
                Batal
            </button>
            <button type="submit" form="formEditPendaftar" class="btn-primary" style="flex:1;">
                💾 Simpan Perubahan
            </button>
        </div>
    </div>
</div>