<div class="hidden fixed bottom-24 right-4 z-40
            w-[min(94vw,380px)] h-[min(72vh,540px)]
            bg-white rounded-2xl shadow-2xl border border-gray-200
            flex-col overflow-hidden"
     id="chat-panel">

    <!-- ---------- Header ---------- -->
    <div class="bg-primary text-white px-4 py-3
                flex items-center justify-between">

        <div>
            <b class="text-sm">iBITS HELP DESK</b>
            <div class="text-xs text-secondary">AI ASSISTANT</div>
        </div>

        <div class="flex gap-3">
            <button aria-label="Minimize" onclick="toggleChat(false)">
                <i class="fa-solid fa-minus"></i>
            </button>
            <button aria-label="Close" onclick="toggleChat(false)">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>


    <!-- ---------- Messages ---------- -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50"
         id="chat-msgs"></div>


    <!-- ---------- Input ---------- -->
    <form class="p-3 border-t border-gray-200 flex gap-2 bg-white"
          onsubmit="sendChat(event)">

        <input autocomplete="off"
               class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm"
               id="chat-in"
               placeholder="Type your question..." />

        <button aria-label="Send"
                class="btn-secondary px-4 rounded-lg text-sm"
                id="chat-send">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>  