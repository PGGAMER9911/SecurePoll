<?php
$pageTitle = htmlspecialchars($profileUser['name']) . "'s Profile - SecurePoll";
ob_start();
?>
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Profile Header - Compact and Themed -->
    <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 shadow dark:shadow-2xl relative overflow-hidden">
        <!-- Subtle Pattern Background -->
        <div class="absolute inset-0 opacity-50 dark:opacity-10 pointer-events-none bg-[radial-gradient(circle_at_2px_2px,theme(colors.slate.300)_1px,transparent_0)] dark:bg-[radial-gradient(circle_at_2px_2px,white_1px,transparent_0)]" style="background-size: 24px 24px;"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-blue-500/5 dark:from-indigo-500/10 dark:to-blue-500/5 pointer-events-none"></div>
        
        <div class="relative shrink-0">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 p-[2px] shadow-lg shadow-indigo-500/30">
                <div class="w-full h-full bg-white dark:bg-slate-900 rounded-[14px] flex items-center justify-center">
                    <span class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white"><?php echo strtoupper(substr($profileUser['name'], 0, 1)); ?></span>
                </div>
            </div>
        </div>
        
        <div class="relative flex-1 text-left z-10 pt-2 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight text-center sm:text-left"><?php echo htmlspecialchars($profileUser['name']); ?></h1>
                <?php if (\app\Helpers\Session::get('user_id') == $profileUser['id']): ?>
                    <a href="/SecurePoll/public/settings" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-all self-center sm:self-auto">
                        <i data-lucide="settings" class="w-4 h-4"></i> Edit Profile
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($profileUser['bio'])): ?>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-4 leading-relaxed max-w-2xl text-center sm:text-left">
                    <?php echo nl2br(htmlspecialchars($profileUser['bio'])); ?>
                </p>
            <?php endif; ?>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-slate-600 dark:text-slate-400 text-sm font-medium">
                <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i> Member since <?php echo date('M Y', strtotime($profileUser['created_at'])); ?></span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700 hidden sm:block"></span>
                <span class="flex items-center gap-1.5"><i data-lucide="bar-chart-2" class="w-4 h-4 text-indigo-500 dark:text-indigo-400"></i> <strong class="text-slate-900 dark:text-slate-200"><?php echo count($polls); ?></strong> Public Polls</span>
                <?php if (isset($totalVotes) && $totalVotes > 0): ?>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700 hidden sm:block"></span>
                    <span class="flex items-center gap-1.5"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 dark:text-emerald-400"></i> <strong class="text-slate-900 dark:text-slate-200"><?php echo number_format($totalVotes); ?></strong> Votes Received</span>
                <?php endif; ?>
            </div>

            <?php 
            $hasAcademic = !empty($profileUser['college']) || !empty($profileUser['department']) || !empty($profileUser['course']) || !empty($profileUser['semester']) || !empty($profileUser['enrollment_id']);
            if ($hasAcademic): 
            ?>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex flex-wrap items-center justify-center sm:justify-start gap-2 text-xs">
                    <?php if (!empty($profileUser['college'])): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                            <i data-lucide="building" class="w-3.5 h-3.5 text-indigo-500"></i> <?php echo htmlspecialchars($profileUser['college']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($profileUser['department'])): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                            <i data-lucide="book-open" class="w-3.5 h-3.5 text-indigo-500"></i> <?php echo htmlspecialchars($profileUser['department']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($profileUser['course'])): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                            <i data-lucide="award" class="w-3.5 h-3.5 text-indigo-500"></i> <?php echo htmlspecialchars($profileUser['course']); ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($profileUser['semester'])): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700">
                            Semester <?php echo (int)$profileUser['semester']; ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($profileUser['enrollment_id'])): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700 font-mono">
                            #<?php echo htmlspecialchars($profileUser['enrollment_id']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Public Polls Section -->
    <div class="space-y-6">
        <div class="flex items-center gap-3 pb-2 border-b border-slate-200 dark:border-white/5">
            <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                <i data-lucide="list" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Public Polls</h2>
        </div>

        <?php if (empty($polls)): ?>
            <!-- Empty State -->
            <div class="bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/60 rounded-xl p-10 sm:p-16 flex flex-col items-center justify-center text-center shadow-sm dark:shadow-none">
                <div class="w-16 h-16 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center mb-5 shadow-inner">
                    <i data-lucide="inbox" class="w-8 h-8 text-indigo-400 opacity-80"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">No Public Polls Yet</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto leading-relaxed">
                    <?php echo htmlspecialchars($profileUser['name']); ?> hasn't published any public polls for the community to vote on.
                </p>
            </div>
        <?php else: ?>
            <!-- Poll Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($polls as $poll): ?>
                    <div class="group relative bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl overflow-hidden shadow-sm dark:shadow-lg hover:shadow dark:hover:shadow-2xl dark:hover:shadow-indigo-500/10 hover:border-indigo-300 dark:hover:border-indigo-500/30 transition-all duration-300 hover:-translate-y-1 flex flex-col">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        
                        <div class="p-6 flex-1 flex flex-col relative z-10">
                            <div class="flex justify-between items-start mb-4 gap-3">
                                <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors line-clamp-2" title="<?php echo htmlspecialchars($poll['title']); ?>">
                                    <?php echo htmlspecialchars($poll['title']); ?>
                                </h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shrink-0 shadow-inner">
                                    <i data-lucide="globe" class="w-3 h-3"></i> Public
                                </span>
                            </div>
                            
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 flex-1 line-clamp-3 leading-relaxed">
                                <?php echo htmlspecialchars($poll['description'] ?: 'No description provided.'); ?>
                            </p>
                            
                            <div class="mt-auto pt-5 border-t border-slate-100 dark:border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex flex-col gap-1.5 text-xs font-medium text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                        <?php echo date('M j, Y', strtotime($poll['created_at'])); ?>
                                    </span>
                                </div>
                                <a href="/SecurePoll/public/polls/show?id=<?php echo $poll['id']; ?>" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 hover:bg-indigo-500 hover:text-white dark:hover:text-white hover:border-indigo-500 hover:shadow-md dark:hover:shadow-lg dark:hover:shadow-indigo-500/25 transition-all">
                                    <i data-lucide="vote" class="w-4 h-4"></i> Vote Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
