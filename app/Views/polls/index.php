<?php
use app\Helpers\Session;

$pageTitle = 'Polls - SecurePoll';
$pageHeader = 'Active Polls';
ob_start();
?>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Public Polls</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Discover and participate in active community polls.</p>
    </div>
    <?php if (Session::get('user_id')): ?>
        <a href="/SecurePoll/public/polls/create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-5 rounded-xl transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-indigo-500/25 dark:hover:shadow-indigo-500/40 border border-transparent">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Create Poll
        </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php if (empty($polls)): ?>
        <div class="col-span-full py-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-700/50 rounded-xl bg-white dark:bg-slate-900/30 backdrop-blur-sm shadow-sm dark:shadow-none">
            <div class="bg-indigo-50 dark:bg-indigo-500/10 p-5 rounded-full mb-5 border border-indigo-100 dark:border-indigo-500/20 shadow-sm dark:shadow-inner">
                <i data-lucide="inbox" class="w-12 h-12 text-indigo-500 dark:text-indigo-400"></i>
            </div>
            <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">No active polls found</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm text-center max-w-sm">There are currently no public polls available to vote on. Check back later or create your own.</p>
        </div>
    <?php else: ?>
        <?php foreach ($polls as $poll): ?>
            <?php $pollStatus = \app\Services\PollService::getLifecycleStatus($poll); ?>
            <div class="group relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl overflow-hidden shadow dark:shadow-lg hover:shadow-md hover:-translate-y-1 hover:border-indigo-200 dark:hover:shadow-2xl dark:hover:shadow-indigo-500/10 dark:hover:border-indigo-500/30 transition-all duration-300 flex flex-col">
                <!-- Subtle gradient overlay on hover -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                
                <div class="p-6 flex-1 flex flex-col relative z-10">
                    <div class="flex justify-between items-start mb-4 gap-3">
                        <h5 class="font-bold text-slate-900 dark:text-white text-lg leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors line-clamp-2" title="<?php echo htmlspecialchars($poll['title']); ?>">
                            <?php echo htmlspecialchars($poll['title']); ?>
                        </h5>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $pollStatus === 'Active' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : ($pollStatus === 'Closed' ? 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20'); ?> shrink-0 shadow-sm dark:shadow-inner">
                                <i data-lucide="<?php echo $pollStatus === 'Active' ? 'activity' : ($pollStatus === 'Closed' ? 'lock' : 'clock'); ?>" class="w-3 h-3"></i> <?php echo htmlspecialchars($pollStatus); ?>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shrink-0 shadow-sm dark:shadow-inner">
                                <i data-lucide="globe" class="w-3 h-3"></i> Public
                            </span>
                        </div>
                    </div>
                    
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex-1 line-clamp-3 leading-relaxed">
                        <?php echo htmlspecialchars($poll['description'] ?: 'No description provided.'); ?>
                    </p>
                    
                    <div class="mt-auto pt-5 border-t border-slate-200 dark:border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex flex-col gap-1.5 text-xs font-medium text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                <?php if ($pollStatus === 'Upcoming' && !empty($poll['start_time'])): ?>
                                    Starts: <?php echo date('M j, Y \\a\\t g:i A', strtotime($poll['start_time'])); ?>
                                <?php elseif (($pollStatus === 'Closed' || $pollStatus === 'Ended') && !empty($poll['end_time'])): ?>
                                    Closed: <?php echo date('M j, Y \\a\\t g:i A', strtotime($poll['end_time'])); ?>
                                <?php else: ?>
                                    Created <?php echo date('M j, Y', strtotime($poll['created_at'])); ?>
                                <?php endif; ?>
                            </span>
                            <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                Created by 
                                <a href="/SecurePoll/public/profile?id=<?php echo $poll['creator_id']; ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors font-bold truncate max-w-[100px]" title="<?php echo htmlspecialchars($poll['creator_name'] ?? 'Unknown'); ?>">
                                    <?php echo htmlspecialchars($poll['creator_name'] ?? 'Unknown'); ?>
                                </a>
                            </span>
                        </div>
                        <?php if (Session::get('user_id') && $poll['creator_id'] == Session::get('user_id')): ?>
                            <a href="/SecurePoll/public/polls/manage?id=<?php echo $poll['id']; ?>" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 hover:bg-indigo-100 dark:hover:bg-indigo-500 hover:text-indigo-800 dark:hover:text-white hover:border-indigo-300 dark:hover:border-indigo-500 transition-all shadow-sm dark:shadow-none hover:shadow-sm dark:hover:shadow-lg dark:hover:shadow-indigo-500/25">
                                <i data-lucide="settings" class="w-4 h-4"></i> Manage
                            </a>
                        <?php else: ?>

                            <?php if ($pollStatus === 'Upcoming'): ?>
                                <a href="/SecurePoll/public/polls/show?id=<?php echo $poll['id']; ?>" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-all shadow-sm">
                                    <i data-lucide="clock" class="w-4 h-4"></i> Scheduled
                                </a>
                            <?php elseif ($pollStatus === 'Closed' || $pollStatus === 'Ended'): ?>
                                <a href="/SecurePoll/public/polls/show?id=<?php echo $poll['id']; ?>" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-all shadow-sm">
                                    <i data-lucide="lock" class="w-4 h-4"></i> Closed
                                </a>
                            <?php else: ?>
                                <a href="/SecurePoll/public/polls/show?id=<?php echo $poll['id']; ?>" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 hover:bg-indigo-100 dark:hover:bg-indigo-500 hover:text-indigo-800 dark:hover:text-white hover:border-indigo-300 dark:hover:border-indigo-500 transition-all shadow-sm dark:shadow-none hover:shadow-sm dark:hover:shadow-lg dark:hover:shadow-indigo-500/25">
                                    <i data-lucide="vote" class="w-4 h-4"></i> Vote Now
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>

