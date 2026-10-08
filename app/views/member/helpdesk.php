<div class="view-section w-full p-6 lg:p-10" id="view-helpdesk">
<div class="flex flex-wrap justify-between items-center gap-3 mb-6"><div><h1 class="text-2xl font-bold text-primary">STRAW Support / Help Desk</h1><p class="text-textMuted text-sm">Concerns, inquiries and assistance requests.</p></div><div class="flex gap-3"><button class="btn-primary px-5 py-2 rounded-xl text-sm" onclick="toggleChat(true)"><i class="fa-solid fa-robot"></i> AI Assistant</button><button class="btn-secondary px-5 py-2 rounded-xl text-sm" onclick="openTicketForm()"><i class="fa-solid fa-plus"></i> Create New Ticket</button></div></div>
<div class="grid grid-cols-3 gap-3 mb-5" id="tk-counts"></div>
<p class="text-xs text-textMuted mb-3" id="tk-scope"></p>
<div class="space-y-3" id="tk-list"></div>
</div>
