<div class="view-section w-full p-6 lg:p-10" id="view-admin-cbl">
<div class="mb-8">
<h1 class="text-2xl font-bold text-primary dark:text-white">CBL Workflow &amp; Verification</h1>
<p class="text-textMuted text-sm">Review, approve, or reject proposed changes to the Constitution and By-Laws.</p>
</div>
<div class="bg-surface rounded-2xl shadow-sm border border-gray-100 p-6 animate-slide-up">
<h3 class="font-bold text-lg text-primary mb-6 border-b border-gray-100 pb-4">Pending Amendments</h3>
<!-- Workflow Tracker UI -->
<div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
<div class="flex justify-between mb-2">
<span class="text-sm font-bold text-primary">Amendment #2026-04: Attendance Fines</span>
<span class="text-xs font-semibold px-2 py-1 bg-yellow-100 text-yellow-700 rounded">Needs Your Approval</span>
</div>
<p class="text-sm text-gray-600 mb-6">Proposed change to Section 4.1 regarding unexcused absences during general assemblies.</p>
<!-- Progress Bar -->
<div class="relative w-full h-2 bg-gray-200 rounded-full mb-8">
<div class="absolute top-0 left-0 h-2 bg-green-500 rounded-full w-2/3"></div>
<!-- Nodes -->
<div class="absolute -top-3 left-0 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md border-2 border-white"><i class="fa-solid fa-check text-xs"></i></div>
<div class="absolute -top-3 left-1/3 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md border-2 border-white transform -translate-x-1/2"><i class="fa-solid fa-check text-xs"></i></div>
<div class="absolute -top-3 left-2/3 w-8 h-8 rounded-full bg-secondary text-primary flex items-center justify-center shadow-md border-2 border-white transform -translate-x-1/2"><i class="fa-solid fa-spinner animate-spin text-xs"></i></div>
<div class="absolute -top-3 right-0 w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center border-2 border-white"><i class="fa-solid fa-lock text-xs"></i></div>
</div>
<!-- Labels -->
<div class="flex justify-between text-xs font-medium text-gray-500">
<div class="text-center">Initiated<br/><span class="text-[10px]">Sec. Smith</span></div>
<div class="text-center transform -translate-x-4">Adviser<br/><span class="text-[10px] text-green-600">Approved</span></div>
<div class="text-center transform -translate-x-4">President<br/><span class="text-[10px] text-green-600">Approved</span></div>
<div class="text-center">Vice President<br/><span class="text-[10px] text-yellow-600">Pending</span></div>
</div>
</div>
<div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
<button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors text-textDark">View Document</button>
<button class="px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors" onclick="showModal('Reject Amendment', 'Are you sure you want to reject this amendment?')">Reject</button>
<button class="px-4 py-2 btn-primary rounded-lg text-sm font-medium" onclick="showModal('Approve Amendment', 'Approve and publish this amendment to the official CBL?', true)">Approve &amp; Publish</button>
</div>
</div>
</div>
