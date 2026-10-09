<!-- ============================================================
     Custom Modal (replaces alert / confirm)
     ============================================================ -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50
            flex items-center justify-center
            opacity-0 pointer-events-none transition-opacity duration-300"
     id="custom-modal">

    <div class="bg-surface rounded-2xl p-6 shadow-2xl max-w-sm w-full
                transform scale-95 transition-transform duration-300"
         id="modal-content">

        <h3 class="text-lg font-bold text-primary mb-2" id="modal-title">
            Title
        </h3>

        <p class="text-sm text-textMuted mb-6" id="modal-body">
            Message body goes here.
        </p>

        <div class="flex justify-end gap-3">

            <button type="button"
                    class="px-4 py-2 border border-gray-200 rounded-lg
                           text-sm font-medium hover:bg-gray-50"
                    onclick="closeModal()">
                Cancel
            </button>

            <button type="button"
                    class="px-4 py-2 btn-primary rounded-lg text-sm font-medium"
                    id="modal-confirm-btn"
                    onclick="confirmModal()">
                Confirm
            </button>
        </div>
    </div>
</div>