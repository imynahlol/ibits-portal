<!-- ============================================================
     Modal: Event Form (add / edit)
     ============================================================ -->
<div class="hidden fixed inset-0 z-50 bg-black/50
            flex items-center justify-center p-4"
     id="m-form"
     onclick="if (event.target === this) closeM('m-form')">

    <form class="modal-in bg-white rounded-2xl shadow-2xl
                 w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-3"
          id="m-form-c"
          onsubmit="saveEvent(event)">
    </form>
</div>