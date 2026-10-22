<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$pageTitle = 'Notifications'; ?>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
ob_start(); ?>

<?php
    $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$totalCount = count($notifications);
    $allCount = \app\Models\Notification::class ? (new \app\Models\Notification())->getForUser(\app\Helpers\Session::get('user_id'), 1000, 0, 'all') : [];
    $totalAllCount = count($allCount);
    
    // In our backend $unreadCount is passed to the view.
    $hasUnread = $unreadCount > 0;
    $hasAny = $totalAllCount > 0;
?>

<div class="max-w-[1200px] mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-slate-200 dark:border-white/5">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Notifications</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Stay updated on your polls, votes, invitations and account activity.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="<?php echo $basePrefix; ?>/settings/notifications" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <i data-lucide="settings" class="w-4 h-4"></i> Settings
            </a>
            <button onclick="markAllAsReadPage(this)" <?= !$hasUnread ? 'disabled' : '' ?> class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i data-lucide="check-check" class="w-4 h-4"></i> Mark all as read
            </button>
            <button onclick="clearAllNotifications(this)" <?= !$hasAny ? 'disabled' : '' ?> class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i data-lucide="trash-2" class="w-4 h-4 text-rose-500"></i> Clear all
            </button>
        </div>
    </div>

    <!-- Filter Tabs (Segmented Control) -->
    <div class="flex">
        <div class="inline-flex bg-slate-100 dark:bg-slate-800/50 p-1 rounded-lg border border-slate-200 dark:border-white/5">
            <a href="?filter=all" class="flex items-center gap-2 px-4 py-1.5 text-sm font-medium rounded-md transition-colors <?= $filter === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-700/50' ?>">
                All <span class="text-xs font-semibold px-1.5 py-0.5 rounded-md <?= $filter === 'all' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' : 'text-slate-500 dark:text-slate-400' ?>"><?= $totalAllCount ?></span>
            </a>
            <a href="?filter=unread" class="flex items-center gap-2 px-4 py-1.5 text-sm font-medium rounded-md transition-colors <?= $filter === 'unread' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-700/50' ?>">
                Unread 
                <?php if ($unreadCount > 0): ?>
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-md <?= $filter === 'unread' ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' ?>"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Notification Feed -->
    <div class="space-y-3">
        <?php if (empty($notifications)): ?>
            <div class="flex flex-col items-center justify-center p-12 lg:p-16 text-center border border-slate-200 dark:border-white/5 rounded-xl bg-white dark:bg-slate-900 shadow-sm mt-4">
                <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 flex items-center justify-center rounded-xl mb-4 border border-slate-200 dark:border-white/5">
                    <i data-lucide="bell" class="w-6 h-6 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                    <?= $filter === 'unread' ? 'No unread notifications' : 'You\'re all caught up' ?>
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">
                    <?= $filter === 'unread' ? 'You have read all your notifications.' : 'New notifications will appear here when there is activity on your account.' ?>
                </p>
                <?php if ($filter === 'unread' && $totalAllCount > 0): ?>
                    <a href="?filter=all" class="mt-4 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                        View all notifications &rarr;
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-3">
                <?php foreach ($notifications as $n): ?>
                    <?php 
                        $icon = $n['icon'] ?: 'bell';
                        $isUnread = $n['is_read'] == 0;
                        
                        $bgClass = $isUnread 
                            ? 'bg-teal-500/5 dark:bg-teal-950/30' 
                            : 'bg-white dark:bg-[#07181b]/80';
                            
                        $borderClass = $isUnread 
                            ? 'border-teal-500/30 dark:border-teal-500/40' 
                            : 'border-slate-200 dark:border-teal-950/80';
                            
                        $textStrong = $isUnread ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-700 dark:text-slate-300 font-medium';
                        $textMuted = $isUnread ? 'text-slate-600 dark:text-slate-400' : 'text-slate-500 dark:text-slate-500';
                        $iconClass = $isUnread ? 'text-teal-600 dark:text-teal-400 bg-teal-500/10' : 'text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800';
                        
                        $url = '#';
                        if ($n['entity_type'] === 'poll' && $n['entity_id']) {
                            if ($n['action_type'] === 'view_poll') $url = $basePrefix . '/polls/show?id=' . $n['entity_id'];
                            if ($n['action_type'] === 'view_invitations') $url = $basePrefix . '/invitations';
                        }
                    ?>
                    <div class="group relative rounded-xl border <?= $borderClass ?> <?= $bgClass ?> hover:border-slate-300 dark:hover:border-slate-600 transition-colors flex items-stretch shadow-xs" id="notif-row-<?= $n['id'] ?>">
                        <!-- Clickable Area -->
                        <a href="<?= $url ?>" onclick="markRead(<?= $n['id'] ?>)" class="flex-1 flex items-start gap-4 p-4 focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-l-xl">
                            
                            <!-- Icon -->
                            <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-lg <?= $iconClass ?>">
                                <i data-lucide="<?= $icon ?>" class="w-5 h-5"></i>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                    <div class="flex items-center gap-2">
                                        <?php if ($isUnread): ?>
                                            <span class="inline-block w-1.5 h-1.5 bg-indigo-500 rounded-full shrink-0" id="unread-dot-<?= $n['id'] ?>"></span>
                                        <?php endif; ?>
                                        <p class="text-sm <?= $textStrong ?> truncate">
                                            <?= htmlspecialchars($n['title']) ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center text-xs <?= $textMuted ?> whitespace-nowrap shrink-0">
                                        <?php 
                                            $dt = new \DateTime($n['created_at']);
                                            $now = new \DateTime();
                                            $diff = $now->diff($dt);
                                            if ($diff->days == 0) {
                                                if ($diff->h == 0) {
                                                    if ($diff->i == 0) echo 'Just now';
                                                    else echo $diff->i . 'm ago';
                                                } else {
                                                    echo $diff->h . 'h ago';
                                                }
                                            } elseif ($diff->days == 1) {
                                                echo 'Yesterday';
                                            } else {
                                                echo $dt->format('M j, Y');
                                            }
                                        ?>
                                    </div>
                                </div>
                                <p class="text-sm <?= $textMuted ?> mt-1 pr-4"><?= htmlspecialchars($n['message']) ?></p>
                            </div>
                        </a>
                        
                        <!-- Delete Action -->
                        <div class="shrink-0 flex items-center pr-4">
                            <button onclick="deleteNotification(<?= $n['id'] ?>)" class="p-1.5 rounded-md text-slate-400 dark:text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-rose-600 dark:hover:text-rose-400 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-rose-500" aria-label="Delete notification" title="Delete">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function markRead(id) {
        fetch((window.SecurePollBase || '') + '/notifications/markRead', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
        }).then(r => r.json()).then(res => {
            if (res.success) {
                const dot = document.getElementById('unread-dot-' + id);
                if (dot) dot.remove();
                
                const row = document.getElementById('notif-row-' + id);
                if (row) {
                    row.classList.remove('bg-indigo-50/50', 'dark:bg-slate-800', 'border-indigo-200', 'dark:border-indigo-500/30');
                    row.classList.add('bg-white', 'dark:bg-slate-900', 'border-slate-200', 'dark:border-white/5');
                    
                    const title = row.querySelector('.text-slate-900, .dark\\:text-white');
                    if (title) {
                        title.classList.remove('text-slate-900', 'dark:text-white', 'font-semibold');
                        title.classList.add('text-slate-700', 'dark:text-slate-300', 'font-medium');
                    }
                    
                    const iconContainer = row.querySelector('.bg-indigo-100, .dark\\:bg-indigo-900\\/40');
                    if (iconContainer) {
                        iconContainer.classList.remove('text-indigo-600', 'dark:text-indigo-400', 'bg-indigo-100', 'dark:bg-indigo-900/40');
                        iconContainer.classList.add('text-slate-500', 'dark:text-slate-400', 'bg-slate-100', 'dark:bg-slate-800');
                    }
                }
            }
        });
    }

    function markAllAsReadPage(btn) {
        if (btn && btn.disabled) return;
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin shrink-0"></i> <span>Updating...</span>';
            if (window.lucide) window.lucide.createIcons();
        }

        fetch((window.SecurePollBase || '') + '/notifications/markAllRead', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
        }).then(r => r.json()).then(res => {
            if (res.success) window.location.reload();
            else if (btn) {
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                btn.innerHTML = originalHtml;
                if (window.lucide) window.lucide.createIcons();
            }
        }).catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                btn.innerHTML = originalHtml;
                if (window.lucide) window.lucide.createIcons();
            }
        });
    }

    function clearAllNotifications(btn) {
        if (btn && btn.disabled) return;
        if (confirm("Are you sure you want to clear all notifications?")) {
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin shrink-0 text-rose-500"></i> <span>Clearing...</span>';
                if (window.lucide) window.lucide.createIcons();
            }

            fetch((window.SecurePollBase || '') + '/notifications/clearAll', {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
            }).then(r => r.json()).then(res => {
                if (res.success) window.location.reload();
                else if (btn) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                }
            }).catch(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                }
            });
        }
    }

    function deleteNotification(id) {
        fetch((window.SecurePollBase || '') + '/notifications/delete', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
        }).then(r => r.json()).then(res => {
            if (res.success) {
                const row = document.getElementById('notif-row-' + id);
                if (row) {
                    row.style.display = 'none';
                    row.remove();
                    // Check if list is empty now
                    if (document.querySelectorAll('[id^="notif-row-"]').length === 0) {
                        window.location.reload();
                    }
                }
            }
        });
    }
</script>

<?php 
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
