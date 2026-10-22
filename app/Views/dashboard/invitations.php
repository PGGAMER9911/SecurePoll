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

<?php if (empty($invitations)): ?>
<div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl shadow-xs overflow-hidden min-h-[360px] flex flex-col items-center justify-center p-8 text-center">
    <div class="w-16 h-16 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-5 shadow-xs">
        <i data-lucide="mail-open" class="w-8 h-8"></i>
    </div>
    
    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Pending Invitations</h3>
    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto mb-6">
        You're all caught up! When an administrator or team invites you to participate in a restricted private election, your access token will appear here.
    </p>
    
    <a href="<?php echo $basePrefix; ?>/polls" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 transition-colors shadow-xs">
        Browse Public Showcase
    </a>
</div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($invitations as $invitation): 
            $statusColor = 'text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700';
            if ($invitation['status'] === 'Pending') $statusColor = 'text-amber-700 dark:text-amber-300 bg-amber-500/10 border-amber-500/20';
            elseif ($invitation['status'] === 'Accepted') $statusColor = 'text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 border-emerald-500/20';
            elseif ($invitation['status'] === 'Declined') $statusColor = 'text-red-700 dark:text-red-300 bg-red-500/10 border-red-500/20';
        ?>
            <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl shadow-xs p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-all hover:bg-slate-50/60 dark:hover:bg-slate-900/40" id="invitation-<?php echo $invitation['id']; ?>">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 mb-1"><?php echo htmlspecialchars($invitation['poll_title']); ?></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Invited by <span class="text-slate-800 dark:text-slate-200 font-semibold"><?php echo htmlspecialchars($invitation['creator_name']); ?></span></p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo $statusColor; ?>">
                            <?php echo htmlspecialchars($invitation['status']); ?>
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500"><i data-lucide="calendar" class="w-3 h-3 inline-block -mt-0.5"></i> <?php echo date('M j, Y', strtotime($invitation['sent_at'])); ?></span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <?php if ($invitation['status'] === 'Pending'): ?>
                        <button onclick="handleInvitation(<?php echo $invitation['id']; ?>, 'accept', this)" class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-xs font-semibold bg-teal-600 hover:bg-teal-500 text-white transition-colors shadow-xs">
                            Accept
                        </button>
                        <button onclick="handleInvitation(<?php echo $invitation['id']; ?>, 'decline', this)" class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                            Decline
                        </button>
                    <?php elseif ($invitation['status'] === 'Accepted'): ?>
                        <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo $invitation['poll_id']; ?>" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-semibold bg-teal-600 hover:bg-teal-500 text-white transition-colors shadow-xs text-center flex items-center justify-center gap-1.5">
                            <span>Open Ballot</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

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

