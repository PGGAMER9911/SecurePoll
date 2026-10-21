<?php
use app\Helpers\Security;

$pageTitle = 'Manage Poll - SecurePoll';
$pageHeader = 'Poll Management';
ob_start();
$options = $poll['options'] ?? [];
$totalVotes = array_sum(array_column($options, 'votes'));
?>
<link rel="stylesheet" id="flatpickr-theme-manage" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    html.dark .flatpickr-calendar {
        background: #1e293b;
        border: 1px solid #334155;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    }
    .flatpickr-day.selected {
        background: #6366f1 !important;
        border-color: #6366f1 !important;
    }
</style>
<script>
    function updateFlatpickrThemeManage() {
        if (document.documentElement.classList.contains('dark')) {
            document.getElementById('flatpickr-theme-manage').href = "https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css";
        } else {
            document.getElementById('flatpickr-theme-manage').href = "https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css";
        }
    }
    document.addEventListener('DOMContentLoaded', updateFlatpickrThemeManage);
    document.addEventListener('themeChanged', updateFlatpickrThemeManage);
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight line-clamp-1"><?php echo htmlspecialchars($poll['title']); ?></h2>
        <div class="flex items-center gap-3 mt-2 text-sm">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                <i data-lucide="<?php echo $poll['visibility'] === 'Public' ? 'globe' : ($poll['visibility'] === 'Private' ? 'lock' : 'link'); ?>" class="w-3 h-3"></i>
                <?php echo htmlspecialchars($poll['visibility']); ?>
            </span>
            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4"></i> Created <?php echo date('M j, Y', strtotime($poll['created_at'])); ?></span>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="/SecurePoll/public/polls/show?id=<?php echo $poll['id']; ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-slate-100/50 dark:bg-slate-800/50 hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-all shadow-sm">
            <i data-lucide="external-link" class="w-4 h-4"></i> View Poll
        </a>
        <button onclick="openEditPollModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500 hover:text-slate-900 dark:hover:text-white hover:border-indigo-500 hover:shadow-sm dark:shadow-lg hover:shadow-indigo-500/25 transition-all">
            <i data-lucide="edit-2" class="w-4 h-4"></i> Edit Poll
        </button>
        <button onclick="document.getElementById('deletePollModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-slate-900 dark:hover:text-white hover:border-red-500 hover:shadow-sm dark:shadow-lg hover:shadow-red-500/25 transition-all">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Poll Details -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Info Card -->
        <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-transparent pointer-events-none"></div>
            <div class="p-6 relative">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i data-lucide="info" class="w-5 h-5 text-indigo-400"></i> Details</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between pb-4 border-b border-white/5">
                        <span class="text-slate-500 dark:text-slate-400">Status</span>
                        <?php $pollStatus = \app\Services\PollService::getLifecycleStatus($poll); ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $pollStatus === 'Active' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : ($pollStatus === 'Closed' ? 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20'); ?> shadow-sm dark:shadow-none">
                            <i data-lucide="<?php echo $pollStatus === 'Active' ? 'activity' : ($pollStatus === 'Closed' ? 'lock' : 'clock'); ?>" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($pollStatus); ?>
                        </span>
                    </div>
                    <div class="flex items-start justify-between pb-4 border-b border-white/5">
                        <span class="text-slate-500 dark:text-slate-400">Description</span>
                        <span class="text-white font-medium text-right max-w-[60%]"><?php echo htmlspecialchars($poll['description'] ?: 'No description provided'); ?></span>
                    </div>
                    <div class="flex justify-between pb-4 border-b border-white/5">
                        <span class="text-slate-500 dark:text-slate-400">Voting Type</span>
                        <span class="text-white font-medium"><?php echo htmlspecialchars($poll['voting_type']); ?></span>
                    </div>
                    <div class="flex justify-between pb-4 border-b border-white/5">
                        <span class="text-slate-500 dark:text-slate-400">Anonymity</span>
                        <span class="text-white font-medium flex items-center gap-1.5">
                            <?php if ($poll['is_anonymous']): ?>
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i> Enabled
                            <?php else: ?>
                                <i data-lucide="shield-off" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i> Disabled
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Total Votes</span>
                        <span class="text-white font-medium flex items-center gap-1.5">
                            <i data-lucide="users" class="w-4 h-4 text-indigo-400"></i> 
                            <?php echo array_sum(array_column($options, 'votes')); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Options Card -->
        <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i data-lucide="list" class="w-5 h-5 text-emerald-400"></i> Poll Options</h3>
                <div class="space-y-3">
                    <?php 
                    $totalVotes = array_sum(array_column($options, 'votes'));
                    foreach ($options as $option): 
                        $percentage = $totalVotes > 0 ? round(($option['votes'] / $totalVotes) * 100) : 0;
                    ?>
                        <div class="p-4 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl relative overflow-hidden group">
                            <!-- Progress Bar Background -->
                            <div class="absolute top-0 left-0 bottom-0 bg-emerald-500/10 transition-all duration-1000 ease-out border-r border-emerald-500/20" style="width: <?php echo $percentage; ?>%"></div>
                            
                            <div class="relative flex justify-between items-center">
                                <span class="font-medium text-slate-900 dark:text-white break-words min-w-0"><?php echo htmlspecialchars($option['option_text']); ?></span>
                                <div class="flex items-center gap-3 shrink-0 ml-3">
                                    <span class="text-sm font-semibold text-emerald-400"><?php echo $percentage; ?>%</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 px-2 py-1 rounded-md shadow-inner border border-slate-700"><?php echo $option['votes']; ?> votes</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Voter Activity Card -->
        <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i data-lucide="activity" class="w-5 h-5 text-purple-400"></i> Voter Activity</h3>
                <div id="voterActivityList" class="space-y-4 pr-2">
                    <div class="flex items-center justify-center py-8 text-sm text-slate-500">
                        <i data-lucide="loader-2" class="w-6 h-6 animate-spin mr-2"></i> Loading activity...
                    </div>
                </div>
                <div id="voterActivityPagination" class="mt-4 text-center hidden">
                    <button id="loadMoreVotersBtn" class="px-4 py-2 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white transition-all shadow-sm">Load More</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Actions & Tools -->
    <div class="space-y-6">
        
        <?php if ($poll['visibility'] === 'Private'): ?>
            <!-- Private Invitation System -->
            <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-indigo-500/20 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none"></div>
                <div class="p-6 relative">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><i data-lucide="user-plus" class="w-5 h-5 text-indigo-400"></i> Invite People</h3>
                    
                    <div class="relative mb-6">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                        <input type="text" id="userSearchInput" placeholder="Search by username or email..." class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-9 pr-4 py-3 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-inner">
                        
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="hidden absolute top-full left-0 right-0 mt-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow dark:shadow-2xl z-50 max-h-60 overflow-y-auto">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                    
                    <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i> Invited People</h4>
                    <div id="invitedUsersList" class="space-y-2 pr-2">
                        <div class="flex items-center justify-center py-4 text-sm text-slate-500">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Loading invitations...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Private Bulk CSV Tool -->
            <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative">
                <div class="p-6 relative">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-2 flex items-center gap-2 text-slate-500 dark:text-slate-400"><i data-lucide="mail-plus" class="w-4 h-4"></i> Bulk CSV Invite</h3>
                    
                    <form id="csvInviteForm" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>" id="csrfToken">
                        <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>" id="pollId">
                        
                        <div class="relative group cursor-pointer">
                            <input type="file" name="csv_file" id="csvFile" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                            <div class="border border-dashed border-slate-600 rounded-xl p-4 text-center bg-white dark:bg-slate-900/50 group-hover:bg-slate-800 transition-all flex flex-col items-center justify-center h-20">
                                <span id="fileName" class="text-xs text-slate-500 dark:text-slate-400 font-medium">Click or drag CSV here</span>
                            </div>
                        </div>
                        
                        <div id="inviteAlert" class="hidden p-3 rounded-lg text-sm border"></div>
                        
                        <button type="submit" id="inviteBtn" class="w-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium py-2 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-md text-sm border border-slate-700">
                            <span id="inviteBtnText">Upload CSV</span>
                            <i data-lucide="loader-2" id="inviteSpinner" class="w-4 h-4 animate-spin hidden"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Share Tool -->
            <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden relative">
                <div class="p-6 relative">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2 flex items-center gap-2"><i data-lucide="share-2" class="w-5 h-5 text-blue-400"></i> Share Poll</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Share this link to allow users to vote.</p>
                    
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1 group">
                            <i data-lucide="link" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500"></i>
                            <input type="text" id="shareUrl" readonly value="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/SecurePoll/public/polls/show?id=" . $poll['id']; ?>" 
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-9 pr-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-inner">
                        </div>
                        <button onclick="copyShareUrl()" class="p-2.5 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white transition-all shadow-sm group relative" title="Copy to clipboard">
                            <i data-lucide="copy" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Live Status Card -->
        <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden p-6 relative">
             <div class="flex items-center gap-3">
                 <div class="relative">
                     <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                     <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-75"></div>
                 </div>
                 <span class="font-semibold text-white">Poll is Active</span>
             </div>
             <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Voters can currently participate in this poll.</p>
        </div>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deletePollModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" onclick="document.getElementById('deletePollModal').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-slate-900 border border-slate-300 dark:border-white/10 rounded-xl p-6 w-full max-w-md shadow dark:shadow-2xl animate-fade-in-up">
        <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center mb-4 border border-red-500/20">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Delete Poll?</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Are you sure you want to permanently delete "<?php echo htmlspecialchars($poll['title']); ?>"? This action cannot be undone and all votes will be lost.</p>
        <div class="flex items-center justify-end gap-3">
            <button onclick="document.getElementById('deletePollModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100/50 dark:bg-slate-800/50 hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700 transition-all">
                Cancel
            </button>
            <form action="/SecurePoll/public/polls/delete" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                <input type="hidden" name="id" value="<?php echo $poll['id']; ?>">
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-500 text-slate-900 dark:text-white hover:bg-red-600 transition-colors shadow-sm dark:shadow-lg shadow-red-500/25 border border-transparent">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function escapeHtml(unsafe) {
    return (unsafe || '').toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function copyShareUrl() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(urlInput.value);
}

function updateFileName(input) {
    const fileNameElement = document.getElementById('fileName');
    if (input.files && input.files.length > 0) {
        fileNameElement.textContent = input.files[0].name;
        fileNameElement.classList.add('text-indigo-400');
    } else {
        fileNameElement.textContent = 'Click or drag CSV here';
        fileNameElement.classList.remove('text-indigo-400');
    }
}

// User Invitation Logic
document.addEventListener('DOMContentLoaded', () => {
    const isPrivate = <?php echo $poll['visibility'] === 'Private' ? 'true' : 'false'; ?>;
    if (!isPrivate) return;

    const pollId = document.getElementById('pollId').value;
    const csrfToken = document.getElementById('csrfToken').value;
    
    const searchInput = document.getElementById('userSearchInput');
    const searchResults = document.getElementById('searchResults');
    const invitedUsersList = document.getElementById('invitedUsersList');
    
    let searchTimeout;

    // Fetch existing invitations
    window.fetchInvitations = function() {
        invitedUsersList.innerHTML = `<div class="flex items-center justify-center py-4 text-sm text-slate-500"><i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Loading invitations...</div>`;
        if (window.lucide) window.lucide.createIcons();
        
        fetch(`/SecurePoll/public/polls/getInvitations?poll_id=${pollId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    invitedUsersList.innerHTML = '';
                    if (data.data.length === 0) {
                        invitedUsersList.innerHTML = `<div class="text-sm text-slate-500 text-center py-4">No one invited yet<br><span class="text-xs mt-1 block">Search for a user above to invite them to this private poll.</span></div>`;
                        return;
                    }

                    data.data.forEach(inv => {
                        let statusColor = 'text-slate-500 dark:text-slate-400 bg-slate-800';
                        let statusText = inv.status;
                        
                        if (inv.status === 'Accepted') {
                            statusColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                            if (inv.has_voted) {
                                statusText = 'Already voted';
                            } else {
                                statusText = 'Access granted &bull; Not voted yet';
                            }
                        }
                        if (inv.status === 'Pending') statusColor = 'text-amber-400 bg-amber-500/10 border-amber-500/20';
                        if (inv.status === 'Declined') statusColor = 'text-red-400 bg-red-500/10 border-red-500/20';
                        if (inv.status === 'Revoked') statusColor = 'text-slate-500 bg-slate-100/50 dark:bg-slate-800/50 border-slate-700';

                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl';
                        div.innerHTML = `
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-medium text-slate-900 dark:text-white truncate">${escapeHtml(inv.user_name || inv.email)}</span>
                                ${inv.user_name ? `<span class="text-xs text-slate-500 truncate">${escapeHtml(inv.email)}</span>` : ''}
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                <span class="text-[11px] font-semibold px-2 py-1 rounded-md border ${statusColor}">${statusText}</span>
                                ${inv.status !== 'Revoked' ? `
                                <button onclick="revokeInvitation(${inv.id}, this)" class="text-slate-500 dark:text-slate-400 hover:text-red-400 transition-colors p-1" title="Revoke Access">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                                ` : ''}
                            </div>
                        `;
                        invitedUsersList.appendChild(div);
                    });
                    
                    if (window.lucide) window.lucide.createIcons();
                } else {
                    invitedUsersList.innerHTML = `
                        <div class="text-sm text-red-400 text-center py-4">
                            Unable to load invitations.
                            <button onclick="fetchInvitations()" class="block mx-auto mt-2 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                        </div>`;
                }
            })
            .catch(err => {
                invitedUsersList.innerHTML = `
                    <div class="text-sm text-red-400 text-center py-4">
                        Couldn't load invitations. Check your connection.
                        <button onclick="fetchInvitations()" class="block mx-auto mt-2 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                    </div>`;
            });
    }
    
    fetchInvitations();

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        
        searchResults.innerHTML = `<div class="p-3 text-sm text-slate-500 dark:text-slate-400 text-center flex items-center justify-center"><i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Searching...</div>`;
        searchResults.classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();

        searchTimeout = setTimeout(() => {
            fetch(`/SecurePoll/public/polls/searchUsers?poll_id=${pollId}&q=${encodeURIComponent(query)}`)
                .then(async res => {
                    const text = await res.text();
                    try {
                        return { ok: res.ok, data: JSON.parse(text) };
                    } catch(e) {
                        return { ok: res.ok, data: null, text: text };
                    }
                })
                .then(result => {
                    const data = result.data;
                    if (data && data.success && data.data && data.data.length > 0) {
                        let newHtml = '';
                        try {
                            data.data.forEach(user => {
                                let actionHtml = '';
                                if (user.is_creator) {
                                    actionHtml = `<span class="text-xs text-slate-500 font-medium">This is your account</span>`;
                                } else if (user.invitation_status === 'Accepted') {
                                    if (user.has_voted) {
                                        actionHtml = `<span class="text-xs text-emerald-400 font-medium"><i data-lucide="check" class="w-3 h-3 inline"></i> Already voted</span>`;
                                    } else {
                                        actionHtml = `<span class="text-xs text-indigo-400 font-medium text-right inline-block">Access granted<br><span class="text-[10px] text-slate-500">Not voted yet</span></span>`;
                                    }
                                } else if (user.invitation_status === 'Pending') {
                                    actionHtml = `<span class="text-xs text-amber-400 font-medium">Pending</span>`;
                                } else if (user.invitation_status === 'Declined') {
                                    actionHtml = `
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-red-400 font-medium">Declined</span>
                                            <button onclick="inviteUser('${escapeHtml(user.full_email)}', this)" class="px-2 py-1 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-slate-900 dark:hover:text-white rounded text-[10px] font-semibold transition-colors">Invite Again</button>
                                        </div>
                                    `;
                                } else {
                                    actionHtml = `
                                    <button onclick="inviteUser('${escapeHtml(user.full_email)}', this)" class="shrink-0 px-3 py-1.5 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-slate-900 dark:hover:text-white rounded-lg text-xs font-semibold transition-colors">
                                        Invite
                                    </button>`;
                                }

                                newHtml += `
                                    <div class="flex items-center justify-between p-3 hover:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700/50 last:border-0">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white truncate">${escapeHtml(user.name)}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 truncate">${escapeHtml(user.masked_email)}</div>
                                        </div>
                                        <div class="ml-3 text-right">
                                            ${actionHtml}
                                        </div>
                                    </div>
                                `;
                            });
                            searchResults.innerHTML = newHtml;
                        } catch(err) {
                            searchResults.innerHTML = `<div class="p-3 text-white">Error: ${escapeHtml(err.message)}</div>`;
                        }
                        
                        searchResults.classList.remove('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    } else if (data && data.success && data.data && data.data.length === 0) {
                        searchResults.innerHTML = '<div class="p-4 text-sm text-slate-500 dark:text-slate-400 text-center">No users found<br><span class="text-xs mt-1 block">Try another name or email.</span></div>';
                    } else {
                        let errorMsg = data && data.message ? escapeHtml(data.message) : (result.text ? escapeHtml(result.text.substring(0, 150)) : 'Unknown API error');
                        searchResults.innerHTML = `
                            <div class="p-3 text-sm text-red-400 text-center">
                                Couldn't search users: <br/><span class="text-xs">${errorMsg}</span>
                                <button onclick="document.getElementById('searchInput').dispatchEvent(new Event('input'))" class="block mx-auto mt-2 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                            </div>`;
                    }
                })
                .catch(err => {
                    searchResults.innerHTML = `
                        <div class="p-3 text-sm text-red-400 text-center">
                            JS Fetch Error: <br/><span class="text-xs">${escapeHtml(err.toString())}</span>
                            <button onclick="document.getElementById('searchInput').dispatchEvent(new Event('input'))" class="block mx-auto mt-2 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                        </div>`;
                });
        }, 300);
    });

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    window.inviteUser = function(email, btnElement) {
        if (btnElement.disabled) return;
        const originalText = btnElement.innerText;
        btnElement.innerText = 'Sending...';
        btnElement.disabled = true;
        btnElement.setAttribute('aria-busy', 'true');

        const formData = new FormData();
        formData.append('poll_id', pollId);
        formData.append('email', email);
        formData.append('csrf_token', csrfToken);

        fetch('/SecurePoll/public/polls/inviteUser', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btnElement.innerText = 'Sent!';
                btnElement.removeAttribute('aria-busy');
                btnElement.classList.replace('text-indigo-400', 'text-emerald-400');
                btnElement.classList.replace('hover:bg-indigo-500', 'hover:bg-emerald-500');
                if (typeof window.fetchInvitations === 'function') window.fetchInvitations();
            } else {
                alert(data.message);
                btnElement.innerText = originalText;
                btnElement.disabled = false;
                btnElement.removeAttribute('aria-busy');
            }
        })
        .catch(err => {
            alert('Failed to send invitation. Check network.');
            btnElement.innerText = originalText;
            btnElement.disabled = false;
            btnElement.removeAttribute('aria-busy');
        });
    };

    window.revokeInvitation = function(invitationId, btnElement) {
        if (btnElement.disabled) return;
        if (!confirm('Are you sure you want to revoke this invitation? The user will immediately lose access.')) return;
        
        btnElement.disabled = true;
        btnElement.setAttribute('aria-busy', 'true');
        btnElement.innerHTML = '<i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin inline-block align-middle"></i>';
        if (window.lucide) window.lucide.createIcons();
        
        const formData = new FormData();
        formData.append('invitation_id', invitationId);
        formData.append('csrf_token', csrfToken);

        fetch('/SecurePoll/public/polls/revokeInvitation', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof window.fetchInvitations === 'function') window.fetchInvitations();
            } else {
                alert(data.message);
                btnElement.disabled = false;
                btnElement.removeAttribute('aria-busy');
                btnElement.innerText = 'Revoke';
            }
        })
        .catch(err => {
            alert('Failed to revoke. Check network.');
            btnElement.disabled = false;
            btnElement.removeAttribute('aria-busy');
            btnElement.innerText = 'Revoke';
        });
    };
});

// Voter Activity Logic
document.addEventListener('DOMContentLoaded', () => {
    const pollId = document.getElementById('pollId')?.value || new URLSearchParams(window.location.search).get('id');
    if (!pollId) return;

    const activityList = document.getElementById('voterActivityList');
    const paginationWrapper = document.getElementById('voterActivityPagination');
    const loadMoreBtn = document.getElementById('loadMoreVotersBtn');
    const votingType = '<?php echo $poll['voting_type'] ?? ''; ?>';
    
    let currentPage = 1;
    let isLoading = false;

    window.fetchActivity = function(page, append = false) {
        if (isLoading) return;
        isLoading = true;
        
        if (!append) {
            activityList.innerHTML = `<div class="flex items-center justify-center py-8 text-sm text-slate-500"><i data-lucide="loader-2" class="w-6 h-6 animate-spin mr-2"></i> Loading activity...</div>`;
            if (window.lucide) window.lucide.createIcons();
            paginationWrapper.classList.add('hidden');
        } else {
            loadMoreBtn.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline-block mr-2"></i> Loading...`;
            if (window.lucide) window.lucide.createIcons();
            loadMoreBtn.disabled = true;
        }

        fetch(`/SecurePoll/public/polls/voterActivity?poll_id=${pollId}&page=${page}`)
            .then(res => res.json())
            .then(data => {
                if (!append) activityList.innerHTML = '';
                
                if (data.success) {
                    if (data.data.length === 0 && !append) {
                        activityList.innerHTML = `
                            <div class="text-sm text-slate-500 text-center py-10 flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100/50 dark:bg-slate-800/50 flex items-center justify-center mb-3 border border-slate-200 dark:border-slate-700/50 shadow-inner">
                                    <i data-lucide="activity" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i>
                                </div>
                                <span class="text-slate-700 dark:text-slate-300 font-semibold text-base mb-1">No votes yet</span>
                                <span>When people vote on this poll, their activity will appear here.</span>
                            </div>`;
                        paginationWrapper.classList.add('hidden');
                        if (window.lucide) window.lucide.createIcons();
                    } else {
                        data.data.forEach(voter => {
                            const isAnonymous = data.is_anonymous;
                            const avatarChar = isAnonymous ? '?' : (voter.name ? voter.name.charAt(0).toUpperCase() : '?');
                            const profileLink = (isAnonymous || !voter.voter_user_id) ? '' : `href="/SecurePoll/public/profile?id=${voter.voter_user_id}" class="hover:text-indigo-400 transition-colors"`;
                            const dateObj = new Date(voter.vote_time);
                            const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' &bull; ' + dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                            
                            let optionsHtml = '';
                            if (votingType === 'Rating') {
                                optionsHtml = `<span class="text-slate-900 dark:text-white font-medium text-lg">${escapeHtml(voter.selected_options[0])} <span class="text-slate-500 text-sm">/ 10</span></span>`;
                            } else if (votingType === 'Yes/No') {
                                optionsHtml = `<span class="text-slate-900 dark:text-white font-medium">Answer: ${escapeHtml(voter.selected_options[0])}</span>`;
                            } else {
                                if (voter.selected_options.length === 1) {
                                    optionsHtml = `<span class="text-slate-900 dark:text-white font-medium">Selected: ${escapeHtml(voter.selected_options[0])}</span>`;
                                } else {
                                    optionsHtml = `<span class="text-xs uppercase font-semibold tracking-wider text-slate-500 block mb-1">Selected:</span><ul class="list-disc pl-4 mt-1 space-y-1 text-slate-900 dark:text-white font-medium">`;
                                    voter.selected_options.forEach(opt => {
                                        optionsHtml += `<li>${escapeHtml(opt)}</li>`;
                                    });
                                    optionsHtml += `</ul>`;
                                }
                            }

                            const div = document.createElement('div');
                            div.className = 'bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl p-4 flex gap-4 items-start shadow-sm hover:border-indigo-500/30 transition-colors';
                            
                            div.innerHTML = `
                                <div class="w-10 h-10 shrink-0 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold shadow-inner relative overflow-hidden">
                                    ${isAnonymous ? '<div class="absolute inset-0 bg-slate-100/80 dark:bg-slate-800/80 backdrop-blur-[1px] flex items-center justify-center"><i data-lucide="lock" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i></div>' : avatarChar}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm truncate flex items-center gap-1.5">
                                            ${profileLink ? `<a ${profileLink}>${escapeHtml(voter.name)}</a>` : escapeHtml(voter.name)}
                                        </div>
                                        <div class="text-[11px] text-slate-500 shrink-0 flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i> ${formattedDate}
                                        </div>
                                    </div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400">
                                        ${optionsHtml}
                                    </div>
                                </div>
                            `;
                            activityList.appendChild(div);
                        });

                        if (data.data.length === 50) {
                            paginationWrapper.classList.remove('hidden');
                        } else {
                            paginationWrapper.classList.add('hidden');
                        }
                    }
                } else {
                    if (!append) {
                        activityList.innerHTML = `
                            <div class="text-sm text-red-400 text-center py-8">
                                Unable to load voter activity.
                                <br><span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">${escapeHtml(data.message || 'Please try again.')}</span>
                                <button onclick="fetchActivity(1)" class="block mx-auto mt-4 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                            </div>`;
                        paginationWrapper.classList.add('hidden');
                    }
                }
            })
            .catch(err => {
                if (!append) {
                    activityList.innerHTML = `
                        <div class="text-sm text-red-400 text-center py-8">
                            Couldn't load voter activity. Check your connection and try again.
                            <button onclick="fetchActivity(1)" class="block mx-auto mt-4 text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white transition-colors">Retry</button>
                        </div>`;
                    paginationWrapper.classList.add('hidden');
                }
            })
            .finally(() => {
                isLoading = false;
                loadMoreBtn.innerHTML = 'Load More';
                loadMoreBtn.disabled = false;
                if (window.lucide) window.lucide.createIcons();
            });
    }
    window.fetchActivity(currentPage);

    loadMoreBtn.addEventListener('click', () => {
        currentPage++;
        window.fetchActivity(currentPage, true);
    });
});

// Bulk CSV logic preserved
document.addEventListener('DOMContentLoaded', () => {
    const csvInviteForm = document.getElementById('csvInviteForm');
    if (csvInviteForm) {
        csvInviteForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = document.getElementById('inviteBtn');
            const spinner = document.getElementById('inviteSpinner');
            const alert = document.getElementById('inviteAlert');
            const text = document.getElementById('inviteBtnText');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            text.textContent = 'Uploading...';
            alert.classList.add('hidden');
            
            const formData = new FormData(csvInviteForm);
            
            fetch('/SecurePoll/public/polls/importCsv', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert.classList.remove('hidden');
                if (data.success) {
                    alert.className = 'p-3 rounded-lg text-sm border bg-emerald-500/10 text-emerald-400 border-emerald-500/20 mt-4 backdrop-blur-md';
                    alert.textContent = 'Invitations successfully sent via email!';
                    csvInviteForm.reset();
                    updateFileName(document.getElementById('csvFile'));
                    if (typeof fetchInvitations === 'function') fetchInvitations(); // refresh list
                } else {
                    alert.className = 'p-3 rounded-lg text-sm border bg-red-500/10 text-red-400 border-red-500/20 mt-4 backdrop-blur-md';
                    alert.textContent = data.message || 'Error uploading file.';
                }
            })
            .catch(err => {
                alert.classList.remove('hidden');
                alert.className = 'p-3 rounded-lg text-sm border bg-red-500/10 text-red-400 border-red-500/20 mt-4 backdrop-blur-md';
                alert.textContent = 'A network error occurred.';
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('hidden');
                text.textContent = 'Upload CSV';
            });
        });
    }
});
</script>

<!-- Edit Poll Modal -->
<div id="editPollModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-sm" aria-hidden="true" onclick="closeEditPollModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block relative overflow-visible text-left align-bottom transition-all transform bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow dark:shadow-2xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-blue-500/5 pointer-events-none rounded-xl"></div>
            
            <form id="editPollForm" class="relative">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>">
                
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/5 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2" id="modal-title">
                        <i data-lucide="edit-3" class="w-5 h-5 text-indigo-400"></i> Edit Poll
                    </h3>
                    <button type="button" onclick="closeEditPollModal()" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="px-6 py-6 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    
                    <div id="editAlertBox" class="hidden p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md"></div>
                    
                    <?php if ($totalVotes > 0): ?>
                        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-start gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 mt-0.5 shrink-0"></i>
                            <div>
                                <p class="font-semibold text-sm">Voting has started</p>
                                <p class="text-xs opacity-90 mt-1">This poll already has votes. Structural configurations (Voting Type, Options, Anonymity) are locked to prevent data corruption. You can still safely edit the title, description, visibility, and dates.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- 1. Basic Info -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">1. Basic Information</h4>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Poll Title <span class="text-red-400">*</span></label>
                            <input type="text" name="title" required value="<?php echo htmlspecialchars($poll['title']); ?>" 
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Description (Optional)</label>
                            <textarea name="description" rows="3" 
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner resize-none"><?php echo htmlspecialchars($poll['description']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- 2. Voting Type (Locked if votes > 0) -->
                    <div class="space-y-4 <?php echo $totalVotes > 0 ? 'opacity-60 pointer-events-none' : ''; ?>">
                        <h4 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            2. Voting Type
                            <?php if ($totalVotes > 0): ?><i data-lucide="lock" class="w-4 h-4 text-slate-500"></i><?php endif; ?>
                        </h4>
                        
                        <div class="flex flex-wrap sm:flex-nowrap gap-3">
                            <label class="flex-1 relative flex flex-col p-3 cursor-pointer rounded-xl border <?php echo in_array($poll['voting_type'], ['Single', 'Multiple']) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 hover:bg-slate-50 dark:hover:bg-slate-800/80'; ?> transition-all text-center group edit-type-option" data-type="Custom">
                                <input type="radio" name="voting_type" value="Custom" class="sr-only" <?php echo in_array($poll['voting_type'], ['Single', 'Multiple']) ? 'checked' : ''; ?>>
                                <i data-lucide="list-checks" class="w-5 h-5 mx-auto mb-1 edit-type-icon <?php echo in_array($poll['voting_type'], ['Single', 'Multiple']) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-300'; ?>"></i>
                                <span class="text-sm font-medium edit-type-title <?php echo in_array($poll['voting_type'], ['Single', 'Multiple']) ? 'text-indigo-700 dark:text-white' : 'text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white'; ?>">Custom Options</span>
                            </label>
                            <label class="flex-1 relative flex flex-col p-3 cursor-pointer rounded-xl border <?php echo $poll['voting_type'] === 'Yes/No' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 hover:bg-slate-50 dark:hover:bg-slate-800/80'; ?> transition-all text-center group edit-type-option" data-type="Yes/No">
                                <input type="radio" name="voting_type" value="Yes/No" class="sr-only" <?php echo $poll['voting_type'] === 'Yes/No' ? 'checked' : ''; ?>>
                                <i data-lucide="thumbs-up" class="w-5 h-5 mx-auto mb-1 edit-type-icon <?php echo $poll['voting_type'] === 'Yes/No' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-300'; ?>"></i>
                                <span class="text-sm font-medium edit-type-title <?php echo $poll['voting_type'] === 'Yes/No' ? 'text-indigo-700 dark:text-white' : 'text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white'; ?>">Yes / No</span>
                            </label>
                            <label class="flex-1 relative flex flex-col p-3 cursor-pointer rounded-xl border <?php echo $poll['voting_type'] === 'Rating' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-slate-200 dark:border-slate-700/50 bg-white dark:bg-slate-950/50 hover:bg-slate-50 dark:hover:bg-slate-800/80'; ?> transition-all text-center group edit-type-option" data-type="Rating">
                                <input type="radio" name="voting_type" value="Rating" class="sr-only" <?php echo $poll['voting_type'] === 'Rating' ? 'checked' : ''; ?>>
                                <i data-lucide="star" class="w-5 h-5 mx-auto mb-1 edit-type-icon <?php echo $poll['voting_type'] === 'Rating' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-300'; ?>"></i>
                                <span class="text-sm font-medium edit-type-title <?php echo $poll['voting_type'] === 'Rating' ? 'text-indigo-700 dark:text-white' : 'text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white'; ?>">Rating</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- 3. Configuration (Locked if votes > 0) -->
                    <div class="space-y-4 <?php echo $totalVotes > 0 ? 'opacity-60 pointer-events-none' : ''; ?>">
                        <h4 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            3. Options Configuration
                            <?php if ($totalVotes > 0): ?><i data-lucide="lock" class="w-4 h-4 text-slate-500"></i><?php endif; ?>
                        </h4>
                        
                        <div id="edit-config-Custom" class="edit-config-section <?php echo in_array($poll['voting_type'], ['Single', 'Multiple']) ? '' : 'hidden'; ?>">
                            <div id="editOptionsContainer" class="space-y-3 mb-4">
                                <?php if (in_array($poll['voting_type'], ['Single', 'Multiple'])): ?>
                                    <?php foreach ($options as $idx => $opt): ?>
                                    <div class="flex items-center gap-3 edit-option-row group/opt">
                                        <div class="flex-1">
                                            <input type="text" name="options[]" value="<?php echo htmlspecialchars($opt['option_text']); ?>" placeholder="Option <?php echo $idx+1; ?>" 
                                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                        </div>
                                        <button type="button" class="p-3 text-slate-500 rounded-xl transition-all edit-remove-btn <?php echo count($options) > 2 ? 'hover:bg-red-500/10 hover:text-red-400 cursor-pointer' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo count($options) <= 2 ? 'disabled' : ''; ?>>
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                  
                                  <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 mt-4">
                                      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Choice Mode</label>
                                      <div class="flex gap-6">
                                          <label class="flex items-center gap-3 cursor-pointer group">
                                              <input type="radio" name="choice_mode" value="Single" <?php echo $poll['voting_type'] === 'Single' ? 'checked' : ''; ?> class="w-5 h-5 accent-indigo-500 cursor-pointer">
                                              <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">Single Choice</span>
                                          </label>
                                          <label class="flex items-center gap-3 cursor-pointer group">
                                              <input type="radio" name="choice_mode" value="Multiple" <?php echo $poll['voting_type'] === 'Multiple' ? 'checked' : ''; ?> class="w-5 h-5 accent-indigo-500 cursor-pointer">
                                              <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">Multiple Choice</span>
                                          </label>
                                      </div>
                                  </div>
                                <?php else: ?>
                                    <div class="flex items-center gap-3 edit-option-row group/opt">
                                        <div class="flex-1">
                                            <input type="text" name="options[]" placeholder="Option 1" class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                        </div>
                                        <button type="button" class="p-3 text-slate-500 rounded-xl transition-all edit-remove-btn opacity-50 cursor-not-allowed" disabled><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </div>
                                    <div class="flex items-center gap-3 edit-option-row group/opt">
                                        <div class="flex-1">
                                            <input type="text" name="options[]" placeholder="Option 2" class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                        </div>
                                        <button type="button" class="p-3 text-slate-500 rounded-xl transition-all edit-remove-btn opacity-50 cursor-not-allowed" disabled><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($totalVotes == 0): ?>
                            <button type="button" id="editAddOptionBtn" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-slate-100/50 dark:bg-slate-800/50 hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl transition-all shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i> Add Option
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <div id="edit-config-Yes/No" class="edit-config-section <?php echo $poll['voting_type'] === 'Yes/No' ? '' : 'hidden'; ?>">
                            <div class="text-sm text-slate-500 dark:text-slate-400 bg-slate-100/50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-700">
                                Yes / No options are standard and cannot be altered.
                                <?php if ($poll['voting_type'] === 'Yes/No' && count($options) > 2): ?>
                                <br><span class="text-indigo-400">Includes 3rd option: <?php echo htmlspecialchars($options[2]['option_text']); ?></span>
                                <input type="hidden" name="allow_third_option" value="1">
                                <input type="hidden" name="third_option_label" value="<?php echo htmlspecialchars($options[2]['option_text']); ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div id="edit-config-Rating" class="edit-config-section <?php echo $poll['voting_type'] === 'Rating' ? '' : 'hidden'; ?>">
                            <div class="text-sm text-slate-500 dark:text-slate-400 bg-slate-100/50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-700">
                                Rating scale is locked. Max rating: <?php echo $poll['voting_type'] === 'Rating' && count($options) > 0 ? htmlspecialchars($options[count($options)-1]['option_text']) : '5.0'; ?> Stars.
                                <?php if ($poll['voting_type'] === 'Rating' && count($options) > 0): ?>
                                <input type="hidden" name="rating_scale" value="<?php echo htmlspecialchars($options[count($options)-1]['option_text']); ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 4. Settings & Security -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">4. Settings & Security</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Visibility</label>
                                <div class="relative group">
                                    <!-- Custom Dropdown for Visibility -->
                                    <div class="custom-select-wrapper relative">
                                        <input type="hidden" name="visibility" id="visibilityInput" value="<?php echo htmlspecialchars($poll['visibility']); ?>">
                                        <button type="button" class="custom-select-button w-full flex items-center justify-between bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                            <span class="select-text"><?php echo $poll['visibility'] === 'Public' ? 'Public (Anyone can view)' : ($poll['visibility'] === 'Private' ? 'Private (Invitation only)' : 'Unlisted (Link only)'); ?></span>
                                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 dark:text-slate-400 transition-transform duration-200"></i>
                                        </button>
                                        <div class="custom-select-menu absolute z-50 w-full mt-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl opacity-0 invisible pointer-events-none translate-y-[-10px] transition-all duration-200">
                                            <ul class="py-1">
                                                <li><button type="button" data-value="Public" class="custom-select-option w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:bg-indigo-500/10 transition-colors flex items-center justify-between <?php echo $poll['visibility'] === 'Public' ? 'bg-indigo-500/5 text-indigo-400' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'; ?>">Public (Anyone can view)<i data-lucide="check" class="w-4 h-4 <?php echo $poll['visibility'] === 'Public' ? '' : 'hidden'; ?>"></i></button></li>
                                                <li><button type="button" data-value="Private" class="custom-select-option w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:bg-indigo-500/10 transition-colors flex items-center justify-between <?php echo $poll['visibility'] === 'Private' ? 'bg-indigo-500/5 text-indigo-400' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'; ?>">Private (Invitation only)<i data-lucide="check" class="w-4 h-4 <?php echo $poll['visibility'] === 'Private' ? '' : 'hidden'; ?>"></i></button></li>
                                                <li><button type="button" data-value="Unlisted" class="custom-select-option w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:bg-indigo-500/10 transition-colors flex items-center justify-between <?php echo $poll['visibility'] === 'Unlisted' ? 'bg-indigo-500/5 text-indigo-400' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'; ?>">Unlisted (Link only)<i data-lucide="check" class="w-4 h-4 <?php echo $poll['visibility'] === 'Unlisted' ? '' : 'hidden'; ?>"></i></button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Max Voters (Optional)</label>
                                <input type="number" name="max_voters" min="1" step="1" value="<?php echo $poll['max_voters'] ? htmlspecialchars($poll['max_voters']) : ''; ?>" placeholder="Leave empty for unlimited" 
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all shadow-inner">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Start Time (Optional)</label>
                                <div class="relative">
                                    <input type="text" id="editStartTimeInput" name="start_time" value="<?php echo $poll['start_time'] ? date('Y-m-d H:i', strtotime($poll['start_time'])) : ''; ?>" placeholder="Select start time" 
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl py-3 pl-11 pr-4 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all shadow-inner">
                                    <i data-lucide="calendar" class="w-5 h-5 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">End Time (Optional)</label>
                                <div class="relative">
                                    <input type="text" id="editEndTimeInput" name="end_time" value="<?php echo $poll['end_time'] ? date('Y-m-d H:i', strtotime($poll['end_time'])) : ''; ?>" placeholder="Select end time" 
                                        class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl py-3 pl-11 pr-4 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all shadow-inner">
                                    <i data-lucide="calendar" class="w-5 h-5 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-1">Anonymous Voting</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Keep voter identities private.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer <?php echo $totalVotes > 0 ? 'opacity-60 pointer-events-none' : ''; ?>">
                                <input type="checkbox" name="is_anonymous" value="1" class="sr-only peer" <?php echo $poll['is_anonymous'] ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                        
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/5 bg-white/90 dark:bg-slate-900/80 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" onclick="closeEditPollModal()" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Cancel</button>
                    <button type="submit" id="editPollSubmitBtn" class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-slate-900 dark:text-white rounded-xl text-sm font-medium shadow-sm dark:shadow-lg shadow-indigo-500/25 transition-all">
                        <span id="editPollBtnText">Save Changes</span>
                        <i data-lucide="loader-2" id="editPollSpinner" class="w-4 h-4 ml-2 animate-spin hidden"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

      // Setup Custom Dropdowns
      function setupCustomDropdowns() {
          document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
              const btn = wrapper.querySelector('.custom-select-button');
              const menu = wrapper.querySelector('.custom-select-menu');
              const hiddenInput = wrapper.querySelector('input[type="hidden"]');
              const options = wrapper.querySelectorAll('.custom-select-option');
              const selectText = btn.querySelector('.select-text');
              
              const syncState = () => {
                  const currentValue = hiddenInput.value;
                  options.forEach(o => {
                      const isSelected = o.getAttribute('data-value') === currentValue;
                      const checkIcon = o.querySelector('svg, i');
                      
                      if (isSelected) {
                          o.classList.add('bg-indigo-500/5', 'text-indigo-400');
                          o.classList.remove('text-slate-700', 'dark:text-slate-300');
                          if (checkIcon) checkIcon.classList.remove('hidden');
                          
                          const clone = o.cloneNode(true);
                          const cIcon = clone.querySelector('svg, i');
                          if (cIcon) cIcon.remove();
                          selectText.textContent = clone.textContent.trim();
                      } else {
                          o.classList.remove('bg-indigo-500/5', 'text-indigo-400');
                          o.classList.add('text-slate-700', 'dark:text-slate-300');
                          if (checkIcon) checkIcon.classList.add('hidden');
                      }
                  });
              };
  
              btn.addEventListener('click', (e) => {
                  e.stopPropagation();
                  const isOpen = !menu.classList.contains('invisible');
                  
                  // close all others
                  document.querySelectorAll('.custom-select-menu').forEach(m => {
                      m.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                      const otherIcon = m.parentElement.querySelector('.custom-select-button svg, .custom-select-button i');
                      if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
                  });
                  
                  if (!isOpen) {
                      syncState();
                      menu.classList.remove('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                      const myIcon = btn.querySelector('svg, i');
                      if (myIcon) myIcon.style.transform = 'rotate(180deg)';
                  }
              });
  
              options.forEach(opt => {
                  opt.addEventListener('click', () => {
                      hiddenInput.value = opt.getAttribute('data-value');
                      syncState();
                      hiddenInput.dispatchEvent(new Event('change'));
                      menu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                      const myIcon = btn.querySelector('svg, i');
                      if (myIcon) myIcon.style.transform = 'rotate(0deg)';
                  });
              });
              
              syncState();
          });
          
          document.addEventListener('click', () => {
              document.querySelectorAll('.custom-select-menu').forEach(m => {
                  m.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                  const otherIcon = m.parentElement.querySelector('.custom-select-button svg, .custom-select-button i');
                  if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
              });
          });
      }

      function openEditPollModal() {
    document.getElementById('editPollModal').classList.remove('hidden');
      setupCustomDropdowns();
    // Init flatpickr for modal if not already
    if (!window.editFpStart) {
        window.editFpStart = flatpickr("#editStartTimeInput", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            theme: "dark",
            minDate: "today",
            onOpen: function(selectedDates, dateStr, instance) {
                if(new Date().toDateString() === instance.currentYear + '-' + (instance.currentMonth+1) + '-' + instance.currentDay){
                    instance.set('minTime', new Date());
                } else {
                    instance.set('minTime', null);
                }
            }
        });
        window.editFpEnd = flatpickr("#editEndTimeInput", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            theme: "dark",
            minDate: "today"
        });
    }
}

function closeEditPollModal() {
    document.getElementById('editPollModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const editForm = document.getElementById('editPollForm');
    
    // Type selection logic (only relevant if totalVotes == 0)
    const typeOptions = document.querySelectorAll('.edit-type-option');
    typeOptions.forEach(opt => {
        opt.addEventListener('click', () => {
            typeOptions.forEach(o => {
                // OPTION Unselected
                o.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-500/10');
                o.classList.add('border-slate-200', 'dark:border-slate-700/50', 'bg-white', 'dark:bg-slate-950/50', 'hover:bg-slate-50', 'dark:hover:bg-slate-800/80');
                
                // ICON Unselected
                o.querySelector('.edit-type-icon').classList.remove('text-indigo-600', 'dark:text-indigo-400');
                o.querySelector('.edit-type-icon').classList.add('text-slate-500', 'dark:text-slate-400', 'group-hover:text-indigo-500', 'dark:group-hover:text-indigo-300');
                
                // TITLE Unselected
                o.querySelector('.edit-type-title').classList.remove('text-indigo-700', 'dark:text-white');
                o.querySelector('.edit-type-title').classList.add('text-slate-700', 'dark:text-slate-300', 'group-hover:text-indigo-600', 'dark:group-hover:text-white');
            });
            // OPTION Selected
            opt.classList.remove('border-slate-200', 'dark:border-slate-700/50', 'bg-white', 'dark:bg-slate-950/50', 'hover:bg-slate-50', 'dark:hover:bg-slate-800/80');
            opt.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-500/10');
            
            // ICON Selected
            opt.querySelector('.edit-type-icon').classList.remove('text-slate-500', 'dark:text-slate-400', 'group-hover:text-indigo-500', 'dark:group-hover:text-indigo-300');
            opt.querySelector('.edit-type-icon').classList.add('text-indigo-600', 'dark:text-indigo-400');
            
            // TITLE Selected
            opt.querySelector('.edit-type-title').classList.remove('text-slate-700', 'dark:text-slate-300', 'group-hover:text-indigo-600', 'dark:group-hover:text-white');
            opt.querySelector('.edit-type-title').classList.add('text-indigo-700', 'dark:text-white');
            
            const selectedType = opt.dataset.type;
            document.querySelectorAll('.edit-config-section').forEach(sec => sec.classList.add('hidden'));
            const sec = document.getElementById('edit-config-' + selectedType.replace('/', '\\/'));
            if(sec) sec.classList.remove('hidden');
        });
    });

    // Custom Options Add/Remove
    const addOptBtn = document.getElementById('editAddOptionBtn');
    const container = document.getElementById('editOptionsContainer');
    
    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.edit-option-row');
        rows.forEach(row => {
            const btn = row.querySelector('.edit-remove-btn');
            if (rows.length > 2) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.classList.add('hover:bg-red-500/10', 'hover:text-red-400', 'cursor-pointer');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('hover:bg-red-500/10', 'hover:text-red-400', 'cursor-pointer');
            }
        });
    }

    if (addOptBtn) {
        addOptBtn.addEventListener('click', () => {
            const rowCount = container.querySelectorAll('.edit-option-row').length;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 edit-option-row group/opt';
            row.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="options[]" placeholder="Option ${rowCount + 1}" 
                        class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                </div>
                <button type="button" class="p-3 text-slate-500 rounded-xl transition-all edit-remove-btn hover:bg-red-500/10 hover:text-red-400 cursor-pointer">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            `;
            container.appendChild(row);
            lucide.createIcons({root: row});
            updateRemoveButtons();
        });
    }

    if (container) {
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-remove-btn');
            if (btn && !btn.disabled) {
                btn.closest('.edit-option-row').remove();
                updateRemoveButtons();
                const rows = container.querySelectorAll('.edit-option-row');
                rows.forEach((row, i) => {
                    const input = row.querySelector('input');
                    if (input.placeholder.startsWith('Option')) {
                        input.placeholder = `Option ${i + 1}`;
                    }
                });
            }
        });
    }

    editForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('editPollSubmitBtn');
        const spinner = document.getElementById('editPollSpinner');
        const alert = document.getElementById('editAlertBox');
        const text = document.getElementById('editPollBtnText');
        
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        spinner.classList.remove('hidden');
        text.textContent = 'Saving...';
        alert.classList.add('hidden');
        
        const formData = new FormData(editForm);
        
        fetch('/SecurePoll/public/polls/update', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert.classList.remove('hidden');
            if (data.success) {
                alert.className = 'p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400';
                alert.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i> <div><p class="font-semibold">Success</p><p class="opacity-90 mt-0.5">${data.message}</p></div>`;
                if(window.lucide) window.lucide.createIcons();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert.className = 'p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md bg-red-500/10 border border-red-500/20 text-red-400';
                alert.innerHTML = `<i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i> <div><p class="font-semibold">Error</p><p class="opacity-90 mt-0.5">${data.message}</p></div>`;
                if(window.lucide) window.lucide.createIcons();
            }
        })
        .catch(err => {
            alert.classList.remove('hidden');
            alert.className = 'p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md bg-red-500/10 border border-red-500/20 text-red-400';
            alert.innerHTML = `<i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i> <div><p class="font-semibold">Network Error</p><p class="opacity-90 mt-0.5">Please check your connection and try again.</p></div>`;
            if(window.lucide) window.lucide.createIcons();
        })
        .finally(() => {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            spinner.classList.add('hidden');
            text.textContent = 'Save Changes';
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
