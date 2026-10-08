<!-- View: My Fines -->
<div id="view-fines" class="view-section <?= $currentView === 'fines' ? 'active' : '' ?> w-full p-6 lg:p-10">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-primary dark:text-white">My Fines</h1>
        <p class="text-textMuted text-sm">Track and manage your organizational penalties.</p>
    </div>

    <div class="bg-surface rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
        <div class="p-6 border-b border-gray-100 bg-red-50 flex justify-between items-center">
            <div>
                <h3 class="text-red-800 font-bold">Unpaid Balance</h3>
                <p class="text-sm text-red-600">Please settle your fines before the end of the semester.</p>
            </div>
            <div class="text-2xl font-bold text-red-600" id="fines-total">₱150.00</div>
        </div>

        <div class="p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4 font-medium text-gray-500">Date</th>
                        <th class="p-4 font-medium text-gray-500">Reason</th>
                        <th class="p-4 font-medium text-gray-500">Amount</th>
                        <th class="p-4 font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody id="fines-table">
                    <tr class="border-t border-gray-100">
                        <td class="p-4 text-textDark">Oct 5, 2026</td>
                        <td class="p-4 font-medium text-primary">Late to Gen Assembly</td>
                        <td class="p-4 text-textDark">₱50.00</td>
                        <td class="p-4"><span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold">Unpaid</span></td>
                    </tr>
                    <tr class="border-t border-gray-100">
                        <td class="p-4 text-textDark">Sep 20, 2026</td>
                        <td class="p-4 font-medium text-primary">Absent - IT Week Day 1</td>
                        <td class="p-4 text-textDark">₱100.00</td>
                        <td class="p-4"><span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-bold">Unpaid</span></td>
                    </tr>
                    <tr class="border-t border-gray-100 bg-gray-50 opacity-60">
                        <td class="p-4 text-textDark">Aug 15, 2026</td>
                        <td class="p-4 font-medium text-primary">No ID Lanyard</td>
                        <td class="p-4 text-textDark">₱20.00</td>
                        <td class="p-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Paid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>