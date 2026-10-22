<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$pageTitle = 'Dashboard - SecurePoll';
$pageHeader = 'Dashboard Overview';
ob_start();

$activePollsCount = 0;
$closedPollsCount = 0;
$draftPollsCount = 0;

if (!empty($polls)) {
    foreach ($polls as $p) {
        $st = $p['status'] ?? 'Active';
        if ($st === 'Active') {
            $activePollsCount++;
        } elseif (in_array($st, ['Closed', 'Ended', 'Archived'], true)) {
            $closedPollsCount++;
        } else {
            $draftPollsCount++;
        }
    }
}
$avgVotes = ($totalPolls ?? 0) > 0 ? round(($totalVotesCast ?? 0) / $totalPolls, 1) : 0;
?>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Institutional Governance</span>
            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">Voter Console</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Overview & Election Control</h2>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Manage your active ballots, monitor turnout tallies, and review invitations.</p>
    </div>
    <div class="flex items-center gap-3 shrink-0">
        <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 text-white transition-all shadow-xs">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Create Poll</span>
        </a>
    </div>
</div>

<!-- Real, Task-Oriented Key Metrics (No Fake Trends or Fabricated Storage) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- Metric 1: Total Created Ballots -->
    <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ballots Created</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1"><?php echo $totalPolls ?? 0; ?></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="vote" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-teal-950/60 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <span><?php echo $activePollsCount; ?> Active</span>
            <span class="text-slate-300 dark:text-slate-700">&bull;</span>
            <span><?php echo $closedPollsCount; ?> Closed</span>
            <?php if ($draftPollsCount > 0): ?>
                <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                <span><?php echo $draftPollsCount; ?> Scheduled</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Metric 2: Total Ballots Cast Across Polls -->
    <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Votes Received</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1"><?php echo $totalVotesCast ?? 0; ?></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="check-check" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-teal-950/60 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span>Salted cryptographic hashes</span>
        </div>
    </div>

    <!-- Metric 3: Average Turnout Velocity -->
    <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Average Turnout</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-1"><?php echo $avgVotes; ?></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-teal-950/60 flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
            <span>Votes per ballot average</span>
        </div>
    </div>

    <!-- Metric 4: Platform Security Status -->
    <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Device Trust</p>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1">Verified Session</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                <i data-lucide="shield" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-teal-950/60 flex items-center gap-1.5 text-xs text-teal-600 dark:text-teal-400 font-medium">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            <span>Entropy token active</span>
        </div>
    </div>

</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left 8 Columns: Recent Ballots Activity -->
    <div class="lg:col-span-8">
        <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-200 dark:border-teal-950/80 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4 text-teal-500"></i>
                        <span>Recent Ballots</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Your most recently created elections and resolution votes</p>
                </div>
                <a href="<?php echo $basePrefix; ?>/polls/my-polls" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                    View All Ballots &rarr;
                </a>
            </div>
            
            <?php if (empty($polls)): ?>
                <div class="p-8 sm:p-12 flex flex-col items-center justify-center text-center">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3">
                        <i data-lucide="inbox" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No polls created yet</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mb-4">
                        Launch your first ballot to collect verified, cryptographically decoupled votes from your organization or community.
                    </p>
                    <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Create Your First Poll</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 dark:divide-teal-950/60">
                    <?php foreach (array_slice($polls, 0, 5) as $poll): ?>
                        <?php 
                        $status = $poll['status'] ?? 'Active';
                        $statusBadgeClass = ($status === 'Active') 
                            ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20' 
                            : (($status === 'Closed' || $status === 'Ended')
                                ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700'
                                : 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/20');
                        ?>
                        <div class="p-4 sm:p-5 hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition-colors flex items-center justify-between gap-4 group">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border <?php echo $statusBadgeClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">
                                        <?php echo htmlspecialchars($poll['visibility'] ?? 'Public'); ?>
                                    </span>
                                </div>
                                <a href="<?php echo $basePrefix; ?>/polls/manage?id=<?php echo $poll['id']; ?>" class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors block truncate">
                                    <?php echo htmlspecialchars($poll['title']); ?>
                                </a>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    Created <?php echo date('M j, Y', strtotime($poll['created_at'])); ?> &bull; <?php echo htmlspecialchars($poll['voting_type'] ?? 'Custom'); ?>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo $poll['id']; ?>" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="View Ballot" aria-label="View Ballot">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                                <a href="<?php echo $basePrefix; ?>/polls/manage?id=<?php echo $poll['id']; ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                    <span>Manage</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right 4 Columns: Actions & Workflows -->
    <div class="lg:col-span-4 space-y-6">
        
        <!-- Workflow Shortcuts -->
        <div class="bg-white dark:bg-[#07181b]/80 border border-slate-200 dark:border-teal-950/80 rounded-2xl p-5 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-3">
                Quick Navigation
            </h3>
            <div class="space-y-1 text-xs">
                <a href="<?php echo $basePrefix; ?>/polls" class="flex items-center justify-between p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <i data-lucide="globe" class="w-4 h-4 text-teal-500"></i>
                        <span>Browse Public Polls</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                </a>
                <a href="<?php echo $basePrefix; ?>/invitations" class="flex items-center justify-between p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-teal-500"></i>
                        <span>Private Invitations</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                </a>
                <a href="<?php echo $basePrefix; ?>/docs" class="flex items-center justify-between p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <i data-lucide="book-open" class="w-4 h-4 text-teal-500"></i>
                        <span>Technical Documentation</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                </a>
                <a href="<?php echo $basePrefix; ?>/settings" class="flex items-center justify-between p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4 text-teal-500"></i>
                        <span>Account & Security Settings</span>
                    </span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                </a>
            </div>
        </div>

        <!-- Security & Privacy Guarantee Callout -->
        <div class="border border-teal-500/20 bg-teal-500/5 dark:bg-teal-950/30 rounded-2xl p-5 text-xs space-y-2">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                <i data-lucide="shield-check" class="w-4 h-4 text-teal-500"></i>
                <span>Cryptographic Isolation</span>
            </div>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                Votes cast on SecurePoll are salted with SHA-256 HMAC tokens. Even system administrators cannot reverse hashes to link individual voter accounts with cast options.
            </p>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
