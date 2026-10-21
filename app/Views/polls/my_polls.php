<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$pageTitle = 'My Polls - SecurePoll';
$pageHeader = 'My Polls';
ob_start();
?>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Manage Your Polls</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View, edit, and analyze the polls you have created.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-indigo-500/25">
            <i data-lucide="plus" class="w-4 h-4"></i> Create Poll
        </a>
    </div>
</div>

<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
if (empty($polls)): ?>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-md dark:shadow-xl overflow-hidden relative min-h-[400px] flex flex-col items-center justify-center p-8 text-center">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-transparent pointer-events-none"></div>
        
        <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center mb-6 border border-emerald-100 dark:border-emerald-500/20 relative shadow-sm dark:shadow-none">
            <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
            <i data-lucide="list-checks" class="w-10 h-10 text-emerald-600 dark:text-emerald-400 relative z-10"></i>
        </div>
        
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No Polls Created Yet</h3>
        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-8 font-medium">
            You haven't created any polls yet. Start your first poll to gather insights and opinions from your audience.
        </p>
        
        <a href="<?php echo $basePrefix; ?>/polls/create" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-indigo-500/25">
            Create Your First Poll
        </a>
    </div>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($polls as $poll): ?>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow dark:shadow-xl overflow-hidden hover:shadow-md dark:hover:shadow-2xl hover:-translate-y-1 hover:border-indigo-200 dark:hover:border-indigo-500/30 transition-all group flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <?php $pollStatus = \app\Services\PollService::getLifecycleStatus($poll); ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $pollStatus === 'Active' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : ($pollStatus === 'Closed' ? 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20'); ?> shadow-sm dark:shadow-none">
                            <i data-lucide="<?php echo $pollStatus === 'Active' ? 'activity' : ($pollStatus === 'Closed' ? 'lock' : 'clock'); ?>" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($pollStatus); ?>
                        </span>
                        
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm dark:shadow-none">
                            <i data-lucide="<?php echo $poll['visibility'] === 'Public' ? 'globe' : ($poll['visibility'] === 'Private' ? 'lock' : 'link'); ?>" class="w-3 h-3"></i>
                            <?php echo htmlspecialchars($poll['visibility']); ?>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 line-clamp-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        <?php echo htmlspecialchars($poll['title']); ?>
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 mb-4">
                        <?php echo htmlspecialchars($poll['description'] ?: 'No description provided.'); ?>
                    </p>
                    
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
                        <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4"></i> <?php echo date('M j, Y', strtotime($poll['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-slate-950/50 flex justify-between items-center group-hover:bg-slate-100/50 dark:group-hover:bg-slate-900/80 transition-colors">
                    <a href="<?php echo $basePrefix; ?>/polls/manage?id=<?php echo $poll['id']; ?>" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                        Manage Poll <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
endif; ?>

<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
