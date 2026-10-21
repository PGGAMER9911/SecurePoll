<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
use app\Helpers\Security;

$pageTitle = 'Invitations - SecurePoll';
$pageHeader = 'Manage Invitations';
ob_start();
?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Your Invitations</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage polls you've been invited to or invitations you've sent.</p>
</div>

<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
if (empty($invitations)): ?>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-md dark:shadow-xl overflow-hidden relative min-h-[400px] flex flex-col items-center justify-center p-8 text-center">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-transparent pointer-events-none"></div>
    
    <div class="w-20 h-20 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center mb-6 border border-blue-100 dark:border-blue-500/20 shadow-sm dark:shadow-none relative">
        <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-xl animate-pulse"></div>
        <i data-lucide="mail-open" class="w-10 h-10 text-blue-600 dark:text-blue-400 relative z-10"></i>
    </div>
    
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Pending Invitations</h3>
    <p class="text-slate-500 dark:text-slate-400 font-medium max-w-md mx-auto mb-8">
        You're all caught up! When someone invites you to a private poll, it will appear here.
    </p>
    
    <a href="<?php echo $basePrefix; ?>/polls" class="px-6 py-2.5 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-sm">
        Browse Public Polls
    </a>
</div>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
else: ?>
    <div class="space-y-4">
        <?php foreach ($invitations as $invitation): 
            $statusColor = 'text-slate-600 dark:text-slate-400 bg-slate-100/80 dark:bg-slate-800/80 border-slate-300 dark:border-slate-700';
            if ($invitation['status'] === 'Pending') $statusColor = 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20';
            elseif ($invitation['status'] === 'Accepted') $statusColor = 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20';
            elseif ($invitation['status'] === 'Declined') $statusColor = 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border-red-200 dark:border-red-500/20';
        ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow dark:shadow-md p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all hover:bg-slate-50 dark:hover:bg-slate-800/40 hover:shadow-md dark:hover:shadow-lg hover:-translate-y-[1px]" id="invitation-<?php echo $invitation['id']; ?>">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white line-clamp-1 mb-1"><?php echo htmlspecialchars($invitation['poll_title']); ?></h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Invited by <span class="text-slate-700 dark:text-slate-300 font-bold"><?php echo htmlspecialchars($invitation['creator_name']); ?></span></p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold border <?php echo $statusColor; ?>">
                            <?php echo htmlspecialchars($invitation['status']); ?>
                        </span>
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-500"><i data-lucide="calendar" class="w-3 h-3 inline-block -mt-0.5"></i> <?php echo date('M j, Y', strtotime($invitation['sent_at'])); ?></span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <?php if ($invitation['status'] === 'Pending'): ?>
                        <button onclick="handleInvitation(<?php echo $invitation['id']; ?>, 'accept', this)" class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-emerald-500/20">
                            Accept
                        </button>
                        <button onclick="handleInvitation(<?php echo $invitation['id']; ?>, 'decline', this)" class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-none">
                            Decline`n                        </button>
                    <?php elseif ($invitation['status'] === 'Accepted'): ?>
                        <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo $invitation['poll_id']; ?>" class="w-full sm:w-auto px-5 py-2 rounded-xl text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-indigo-500/20 text-center flex items-center justify-center gap-2">
                            View Poll <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
endif; ?>

<input type="hidden" id="csrfToken" value="<?php echo Security::generateCsrfToken(); ?>">

<script>
function handleInvitation(id, action, btnElement) {
    if (btnElement.disabled) return;
    const originalText = btnElement.innerText;
    btnElement.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline-block mr-1 align-middle"></i> <span class="align-middle">' + (action === 'accept' ? 'Accepting...' : 'Declining...') + '</span>';
    btnElement.disabled = true;
    btnElement.setAttribute('aria-busy', 'true');
    if (window.lucide) window.lucide.createIcons();

    const csrfToken = document.getElementById('csrfToken').value;
    const formData = new FormData();
    formData.append('invitation_id', id);
    formData.append('csrf_token', csrfToken);

    fetch((window.SecurePollBase || '') + '/invitations/' + action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred.');
            btnElement.innerText = originalText;
            btnElement.disabled = false;
            btnElement.removeAttribute('aria-busy');
            if (window.lucide) window.lucide.createIcons();
        }
    })
    .catch(err => {
        alert('Network error.');
        btnElement.innerText = originalText;
        btnElement.disabled = false;
        btnElement.removeAttribute('aria-busy');
        if (window.lucide) window.lucide.createIcons();
    });
}
</script>

<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>

