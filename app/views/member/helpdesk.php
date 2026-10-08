<!-- View: Help Desk -->
<div id="view-helpdesk" class="view-section <?= $currentView === 'helpdesk' ? 'active' : '' ?> w-full p-6 lg:p-10">
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">STRAW Support / Help Desk</h1>
            <p class="text-textMuted text-sm">Concerns, inquiries and assistance requests.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleChat(true)" class="btn-primary px-5 py-2 rounded-xl text-sm">
                <i class="fa-solid fa-robot"></i> AI Assistant
            </button>
            <button onclick="openTicketForm()" class="btn-secondary px-5 py-2 rounded-xl text-sm">
                <i class="fa-solid fa-plus"></i> Create New Ticket
            </button>
        </div>
    </div>

    <div id="tk-counts" class="grid grid-cols-3 gap-3 mb-5"></div>
    <p id="tk-scope" class="text-xs text-textMuted mb-3"></p>
    <div id="tk-list" class="space-y-3"></div>
</div>