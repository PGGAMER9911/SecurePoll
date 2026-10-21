<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$pageTitle = 'Dashboard - SecurePoll';
$pageHeader = 'Dashboard Overview';
ob_start();
?>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Welcome Back!</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Here is a summary of your activity and SecurePoll platform insights.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px] dark:shadow-lg dark:shadow-indigo-500/25">
            <i data-lucide="plus" class="w-4 h-4"></i> Create Poll
        </a>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat 1 -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative overflow-hidden group shadow dark:shadow-none">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-1">Total Polls</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $totalPolls ?? 0; ?></h3>
            </div>
            <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 text-indigo-600 dark:text-indigo-400 shadow-sm dark:shadow-none">
                <i data-lucide="pie-chart" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mr-2"><i data-lucide="trending-up" class="w-3 h-3"></i> 0%</span>
            from last month
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative overflow-hidden group shadow dark:shadow-none">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent pointer-events-none"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-1">Active Votes</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $totalVotesCast ?? 0; ?></h3>
            </div>
            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 text-blue-600 dark:text-blue-400 shadow-sm dark:shadow-none">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mr-2"><i data-lucide="trending-up" class="w-3 h-3"></i> 0%</span>
            from last month
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative overflow-hidden group shadow dark:shadow-none">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-1">Response Rate</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo ($totalPolls ?? 0) > 0 ? 'Good' : 'N/A'; ?></h3>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 shadow-sm dark:shadow-none">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span class="text-slate-400 dark:text-slate-500 flex items-center gap-1 mr-2"><i data-lucide="minus" class="w-3 h-3"></i> 0%</span>
            from last month
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative overflow-hidden group shadow dark:shadow-none">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent pointer-events-none"></div>
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-1">Storage Used</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format((($totalPolls ?? 0) * 0.1), 1); ?> MB</h3>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 text-amber-600 dark:text-amber-400 shadow-sm dark:shadow-none">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-xs text-slate-500 dark:text-slate-400 font-medium">
            <span class="text-slate-500 dark:text-slate-500 flex items-center gap-1 mr-2">Within limits</span>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Recent Activity -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-md dark:shadow-xl overflow-hidden h-full">
            <div class="p-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-transparent">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2"><i data-lucide="list" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i> Recent Activity</h3>
                <a href="<?php echo $basePrefix; ?>/polls/my-polls" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">View All</a>
            </div>
            
            <?php if (empty($polls)): ?>
            <div class="p-6 flex flex-col items-center justify-center min-h-[300px] text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4 border border-slate-200 dark:border-white/5 shadow-sm dark:shadow-none">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h4 class="text-slate-900 dark:text-white font-bold mb-1">No recent activity</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400">You haven't created or participated in any polls yet.</p>
                <a href="<?php echo $basePrefix; ?>/polls/create" class="mt-6 px-4 py-2 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-white transition-all shadow-sm hover:-translate-y-[1px] active:translate-y-[1px]">
                    Get Started
                </a>
            </div>
            <?php else: ?>
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                <?php foreach (array_slice($polls, 0, 5) as $poll): ?>
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg <?php echo $poll['status'] === 'Active' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20'; ?> flex items-center justify-center shadow-sm dark:shadow-none">
                                <i data-lucide="<?php echo $poll['status'] === 'Active' ? 'activity' : 'clock'; ?>" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors line-clamp-1"><?php echo htmlspecialchars($poll['title']); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Created <?php echo date('M j, Y', strtotime($poll['created_at'])); ?></p>
                            </div>
                        </div>
                        <a href="<?php echo $basePrefix; ?>/polls/manage?id=<?php echo $poll['id']; ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white transition-colors border border-transparent hover:border-slate-200 dark:hover:border-transparent">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Links / Actions -->
    <div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-md dark:shadow-xl overflow-hidden h-full">
            <div class="p-6 border-b border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-transparent">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2"><i data-lucide="zap" class="w-5 h-5 text-amber-500 dark:text-amber-400"></i> Quick Actions</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="<?php echo $basePrefix; ?>/polls/create" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all group border border-transparent hover:border-slate-200 dark:hover:border-transparent hover:shadow-sm dark:hover:shadow-none">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform shadow-sm dark:shadow-none">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">Create Poll</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Start a new voting session</p>
                    </div>
                </a>
                
                <a href="<?php echo $basePrefix; ?>/polls/my-polls" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all group border border-transparent hover:border-slate-200 dark:hover:border-transparent hover:shadow-sm dark:hover:shadow-none">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform shadow-sm dark:shadow-none">
                        <i data-lucide="list-checks" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors">My Polls</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">View and manage your polls</p>
                    </div>
                </a>

                <a href="<?php echo $basePrefix; ?>/invitations" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all group border border-transparent hover:border-slate-200 dark:hover:border-transparent hover:shadow-sm dark:hover:shadow-none">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform shadow-sm dark:shadow-none">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors">Invitations</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Manage pending private invites</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
