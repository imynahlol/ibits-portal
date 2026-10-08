<div class="view-section w-full p-6 lg:p-10" id="view-events">
<div class="flex flex-wrap justify-between items-center gap-3 mb-5"><div><h1 class="text-2xl font-bold text-primary">Events / GPOA</h1><p class="text-textMuted text-sm">The organization calendar.</p></div><button class="hidden btn-secondary px-5 py-2 rounded-xl text-sm" id="add-event-btn" onclick="openEventForm()"><i class="fa-solid fa-plus"></i> Add Event</button></div>
<div class="flex gap-2 flex-wrap mb-4" id="year-tabs"></div>
<div class="flex flex-wrap gap-2 mb-4" id="cal-filters"></div>
<div class="grid xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 bg-surface rounded-2xl border border-gray-100 shadow-sm p-4">
<div class="flex items-center justify-between mb-3"><button aria-label="Previous month" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-slate-50" onclick="calMove(-1)"><i class="fa-solid fa-chevron-left"></i></button><div class="text-center"><h2 class="text-lg font-bold text-primary" id="cal-title"></h2><button class="text-xs text-primary font-semibold hover:underline" onclick="calToday()">Today</button></div><button aria-label="Next month" class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-slate-50" onclick="calMove(1)"><i class="fa-solid fa-chevron-right"></i></button></div>
<div class="grid grid-cols-7 text-center text-xs font-semibold text-textMuted mb-1"><div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div></div>
<div class="grid grid-cols-7" id="cal-grid"></div>
<p class="text-xs text-textMuted mt-3" id="cal-count"></p>
</div>
<div class="bg-surface rounded-2xl border border-gray-100 shadow-sm p-5"><h3 class="font-bold text-primary mb-3" id="day-title">Selected day</h3><div class="space-y-2" id="day-list"></div></div>
</div>
<h2 class="text-xl font-bold text-primary mt-10 mb-4">Year Highlights <span class="text-secondaryHover" id="hl-year"></span></h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" id="year-hl"></div>
</div>
