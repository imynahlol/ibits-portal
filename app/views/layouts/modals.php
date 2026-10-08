<!-- Event detail modal -->
<div id="m-event" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeM('m-event')">
    <div id="m-event-c" class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"></div>
</div>

<!-- Event create/edit modal -->
<div id="m-form" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeM('m-form')">
    <form id="m-form-c" class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-3"
          onsubmit="saveEvent(event)"></form>
</div>

<!-- Ticket create modal -->
<div id="m-ticket" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
     onclick="if(event.target===this)closeM('m-ticket')">
    <form class="modal-in bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-3"
          onsubmit="saveTicket(event)">
        <h3 class="text-lg font-bold text-primary">Create New Ticket</h3>
        <input id="t-sub" required placeholder="Subject"
               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
        <select id="t-cat" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
            <option>Internet / Portal Issue</option>
            <option>Account Concern</option>
            <option>Form Request</option>
            <option>Fines Inquiry</option>
            <option>Other</option>
        </select>
        <textarea id="t-desc" required rows="4" placeholder="Describe your concern"
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeM('m-ticket')"
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm">Cancel</button>
            <button class="btn-primary px-4 py-2 rounded-lg text-sm">Submit</button>
        </div>
    </form>
</div>

<!-- Chat FAB + panel -->
<button id="chat-fab" onclick="toggleChat()"
        class="hidden fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-primary text-secondary text-xl shadow-xl flex items-center justify-center hover:scale-105 transition-transform"
        aria-label="Open AI Help Desk">
    <i class="fa-solid fa-headset"></i>
</button>

<div id="chat-panel"
     class="hidden fixed bottom-24 right-4 z-40 w-[min(94vw,380px)] h-[min(72vh,540px)] bg-white rounded-2xl shadow-2xl border border-gray-200 flex-col overflow-hidden">
    <div class="bg-primary text-white px-4 py-3 flex items-center justify-between">
        <div>
            <b class="text-sm">iBITS HELP DESK</b>
            <div class="text-xs text-secondary">AI ASSISTANT</div>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleChat(false)" aria-label="Minimize"><i class="fa-solid fa-minus"></i></button>
            <button onclick="toggleChat(false)" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
    <div id="chat-msgs" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50"></div>
    <form class="p-3 border-t border-gray-200 flex gap-2 bg-white" onsubmit="sendChat(event)">
        <input id="chat-in" autocomplete="off" placeholder="Type your question..."
               class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
        <button id="chat-send" class="btn-secondary px-4 rounded-lg text-sm" aria-label="Send">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>

<!-- Custom modal component -->
<div id="custom-modal"
     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-surface rounded-2xl p-6 shadow-2xl max-w-sm w-full transform scale-95 transition-transform duration-300"
         id="modal-content">
        <h3 id="modal-title" class="text-lg font-bold text-primary mb-2">Title</h3>
        <p id="modal-body" class="text-sm text-textMuted mb-6">Message body goes here.</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeModal()"
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</button>
            <button onclick="confirmModal()" id="modal-confirm-btn"
                    class="px-4 py-2 btn-primary rounded-lg text-sm font-medium">Confirm</button>
        </div>
    </div>
</div>