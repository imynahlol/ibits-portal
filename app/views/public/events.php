<!-- View: Public Events -->
<div id="view-public-events" class="view-section <?= $currentView === 'public-events' ? 'active' : '' ?> w-full min-h-full pt-28 pb-20">
    <div class="container mx-auto px-6 max-w-6xl">
        <h2 class="text-3xl font-bold text-primary mb-2 text-center">Events</h2>
        <p class="text-textMuted text-center mb-10">
            Highlights of past events and upcoming public events. Hover a card for details.
        </p>
        <div id="pub-events" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>
    </div>
</div>