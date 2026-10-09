<!-- ============================================================
     VIEW: STRAW Support / Help Desk
     ============================================================ -->
<div class="view-section w-full p-6 lg:p-10" id="view-helpdesk">

    <!-- ---------- Page header ---------- -->
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-primary">
                STRAW Support / Help Desk
            </h1>
            <p class="text-textMuted text-sm">
                Concerns, inquiries and assistance requests.
            </p>
        </div>

        <div class="flex gap-3">

            <button type="button"
                    class="btn-primary px-5 py-2 rounded-xl text-sm"
                    onclick="toggleChat(true)">
                <i class="fa-solid fa-robot"></i> AI Assistant
            </button>

            <button type="button"
                    class="btn-secondary px-5 py-2 rounded-xl text-sm"
                    onclick="openTicketForm()">
                <i class="fa-solid fa-plus"></i> Create New Ticket
            </button>
        </div>
    </div>


    <!-- ---------- Ticket summary counters ---------- -->
    <div class="grid grid-cols-3 gap-3 mb-5" id="tk-counts"></div>


    <!-- ---------- Scope hint (staff view vs personal) ---------- -->
    <p class="text-xs text-textMuted mb-3" id="tk-scope"></p>


    <!-- ---------- Ticket list ---------- -->
    <div class="space-y-3" id="tk-list"></div>

</div>