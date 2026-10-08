<!-- View: Public Org Structure -->
<div id="view-public-org" class="view-section <?= $currentView === 'public-org' ? 'active' : '' ?> w-full min-h-full pt-28 pb-20">
    <div class="container mx-auto px-6 max-w-6xl">
        <div class="text-center mb-16 animate-fade-in">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Organizational Structure</h2>
            <p class="text-textMuted">Meet the dedicated leaders of iBITS</p>
        </div>

        <!-- Level 1: Adviser -->
        <div class="flex justify-center mb-12 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="text-center hover-card">
                <div class="relative w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary shadow-lg">
                    <img src="https://placehold.co/100x100/06215c/fff?text=AD" alt="Adviser" class="w-full h-full object-cover">
                </div>
                <h4 class="font-bold text-primary">Prof. Alan Turing</h4>
                <p class="text-xs text-textMuted uppercase tracking-wider font-semibold">Organization Adviser</p>
            </div>
        </div>

        <div class="w-full flex justify-center mb-12"><div class="h-8 w-px bg-gray-300"></div></div>

        <!-- Level 2: Executives -->
        <div class="grid grid-cols-2 gap-8 max-w-2xl mx-auto mb-12 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="text-center hover-card">
                <div class="relative w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden border-4 border-secondary shadow-md">
                    <img src="https://placehold.co/100x100/fad517/06215c?text=PR" alt="President" class="w-full h-full object-cover">
                </div>
                <h4 class="font-bold text-primary text-sm">Ada Lovelace</h4>
                <p class="text-xs text-secondaryHover uppercase tracking-wider font-bold">President</p>
            </div>
            <div class="text-center hover-card">
                <div class="relative w-20 h-20 mx-auto mb-4 rounded-full overflow-hidden border-4 border-secondary shadow-md">
                    <img src="https://placehold.co/100x100/fad517/06215c?text=VP" alt="Vice President" class="w-full h-full object-cover">
                </div>
                <h4 class="font-bold text-primary text-sm">Johahnie Gilbuena</h4>
                <p class="text-xs text-secondaryHover uppercase tracking-wider font-bold">Vice President</p>
            </div>
        </div>
    </div>
</div>