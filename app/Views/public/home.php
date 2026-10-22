<?php
use app\Services\PollService;

$pageTitle = 'SecurePoll — Small questions. Different perspectives.';
$pageDescription = 'SecurePoll is a place to ask everyday questions, compare opinions, and run polls with friends, gamers, and small communities. We\'re starting small, learning from real use, and improving step by step.';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
$featuredPoll = !empty($polls) ? $polls[0] : null;
$secondaryPolls = !empty($polls) ? array_slice($polls, 1) : [];

ob_start();
?>

<!-- Grounded Hero Section -->
<section class="relative overflow-hidden border-b border-slate-200/80 dark:border-teal-950/80 bg-white/40 dark:bg-[#071416]">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <!-- Left Column: Editorial Proposition (7 cols) -->
            <div class="lg:col-span-7 space-y-6 text-left">
                
                <!-- Category Tag -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-800 dark:text-teal-300 border border-teal-500/20">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    <span>Early Access &bull; Community Polls</span>
                </div>

                <!-- Primary Heading -->
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                    Small questions.<br class="hidden sm:inline"> Different perspectives.
                </h1>

                <!-- Supporting Description -->
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-normal">
                    SecurePoll is a place to ask everyday questions, compare opinions, and run polls with friends, gamers, and small communities. We're starting small, learning from real use, and improving step by step.
                </p>

                <!-- Action Button Group -->
                <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="#polls" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-all shadow-xs hover:shadow-teal-500/20 min-h-[44px]">
                        <i data-lucide="vote" class="w-4 h-4"></i>
                        <span>Explore Polls</span>
                    </a>
                    
                    <a href="<?php echo $isLoggedIn ? ($basePrefix . '/polls/create') : ($basePrefix . '/login'); ?>" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-teal-900/60 transition-colors min-h-[44px]">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-teal-600 dark:text-teal-400"></i>
                        <span><?php echo $isLoggedIn ? 'Create a Poll' : 'Sign In to Create a Poll'; ?></span>
                    </a>
                </div>

                <!-- Everyday Use Case Highlights Strip -->
                <div class="pt-6 border-t border-slate-200/80 dark:border-teal-950/80 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 dark:text-slate-200">
                            <i data-lucide="gamepad-2" class="w-3.5 h-3.5 text-teal-500 shrink-0"></i>
                            <span>Everyday Topics</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                            Gaming debates, project comparisons, and friendly feedback.
                        </p>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 dark:text-slate-200">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-teal-500 shrink-0"></i>
                            <span>Simple & Private</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                            Salted hashes help protect individual choices in anonymous polls.
                        </p>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900 dark:text-slate-200">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-500 shrink-0"></i>
                            <span>Multiple Formats</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                            Custom choices, Yes/No questions, and 1&ndash;5 star ratings.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Right Column: Live Poll Spotlight (5 cols) -->
            <div class="lg:col-span-5">
                <div class="relative rounded-2xl border border-slate-200 dark:border-teal-900/60 bg-white dark:bg-[#07181b] p-6 sm:p-7 shadow-sm">
                    
                    <!-- Card Top Header -->
                    <div class="flex items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-100 dark:border-teal-950/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold tracking-wider uppercase text-slate-900 dark:text-slate-200">
                                <?php echo $featuredPoll ? 'Featured Community Poll' : 'Ready for New Polls'; ?>
                            </span>
                        </div>
                        <span class="text-[11px] font-mono text-slate-400 dark:text-slate-500">
                            <?php echo $featuredPoll ? ('ID #' . (int)$featuredPoll['id']) : 'STATION: READY'; ?>
                        </span>
                    </div>

                    <?php if ($featuredPoll): ?>
                        <?php
                        $spotlightLifecycle = PollService::getLifecycleStatus($featuredPoll);
                        $spotlightType = match($featuredPoll['voting_type'] ?? 'Single') {
                            'Single' => 'Single Choice',
                            'Multiple' => 'Multiple Choice',
                            'Yes/No' => 'Yes / No',
                            'Rating' => '1-5 Star Rating',
                            default => 'Poll'
                        };
                        ?>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                    <?php echo htmlspecialchars($spotlightLifecycle); ?>
                                </span>
                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                    <?php echo htmlspecialchars($spotlightType); ?>
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-snug line-clamp-2">
                                <?php echo htmlspecialchars($featuredPoll['title']); ?>
                            </h3>

                            <?php if (!empty($featuredPoll['description'])): ?>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                    <?php echo htmlspecialchars($featuredPoll['description']); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-xs text-slate-400 dark:text-slate-500 italic">
                                    Active public poll open for community voting.
                                </p>
                            <?php endif; ?>

                            <!-- Telemetry & Verification Box -->
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-teal-950/80 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                    <span>Created By</span>
                                    <span class="font-medium text-slate-900 dark:text-slate-200 truncate max-w-[160px]">
                                        <?php echo htmlspecialchars($featuredPoll['creator_name'] ?? 'Community Member'); ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                    <span>Privacy Mode</span>
                                    <span class="font-mono text-teal-600 dark:text-teal-400">Salted hash active</span>
                                </div>
                            </div>

                            <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo (int)$featuredPoll['id']; ?>" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                                <span><?php echo ($spotlightLifecycle === 'Closed') ? 'View Results' : 'Vote in Poll'; ?></span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Realistic Idle Preview State -->
                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-teal-950/80 space-y-2 text-xs">
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                    <span>Poll Status</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Online</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                    <span>Vote Privacy</span>
                                    <span class="font-mono text-teal-600 dark:text-teal-400">Salted Hashes Supported</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                    <span>Device Token</span>
                                    <span class="text-slate-700 dark:text-slate-300">Duplicate Check Ready</span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                No public polls are currently active. You can create a poll to ask a question right now.
                            </p>

                            <a href="<?php echo $basePrefix; ?>/polls/create" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Create New Poll</span>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Active Public Polls Showcase -->
<section id="polls" class="py-16 md:py-24 border-b border-slate-200/80 dark:border-teal-950/80 scroll-mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-1">
                    Community Activity
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    Active Public Polls
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Questions and polls currently open for public viewing and voting.
                </p>
            </div>
            <div>
                <a href="<?php echo $basePrefix; ?>/polls" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 transition-colors">
                    <span>View all public polls</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <?php if (empty($polls)): ?>
            <!-- Polished Empty State -->
            <div class="p-10 sm:p-14 rounded-2xl border-2 border-dashed border-slate-200 dark:border-teal-950 bg-white/50 dark:bg-[#07181b]/30 text-center max-w-md mx-auto">
                <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800/40 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">No Active Public Polls</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                    There are no open public polls right now. Sign in to start the first conversation.
                </p>
                <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-all shadow-xs min-h-[44px]">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Create a Poll</span>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($polls as $poll): ?>
                    <?php 
                    $lifecycle = PollService::getLifecycleStatus($poll);
                    $badgeColor = match($lifecycle) {
                        'Active' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20',
                        'Upcoming' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20',
                        default => 'bg-slate-200/70 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-300 dark:border-slate-700'
                    };
                    $typeLabel = match($poll['voting_type'] ?? 'Single') {
                        'Single' => 'Single Choice',
                        'Multiple' => 'Multiple Choice',
                        'Yes/No' => 'Yes / No',
                        'Rating' => '1-5 Rating',
                        default => 'Poll'
                    };
                    ?>
                    <div class="group rounded-2xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/70 p-6 flex flex-col justify-between hover:border-teal-500/40 hover:shadow-md transition-all duration-200">
                        <div>
                            <!-- Header Badges -->
                            <div class="flex items-center justify-between gap-2 mb-3.5">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md border <?php echo $badgeColor; ?>">
                                    <?php if ($lifecycle === 'Active'): ?>
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($lifecycle); ?></span>
                                </span>
                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                    <?php echo htmlspecialchars($typeLabel); ?>
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-300 transition-colors line-clamp-2 mb-2" title="<?php echo htmlspecialchars($poll['title']); ?>">
                                <?php echo htmlspecialchars($poll['title']); ?>
                            </h3>

                            <!-- Description -->
                            <?php if (!empty($poll['description'])): ?>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                    <?php echo htmlspecialchars($poll['description']); ?>
                                </p>
                            <?php else: ?>
                                <p class="text-xs text-slate-400 dark:text-slate-500 italic mb-4">
                                    Community poll open for public responses.
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Footer Meta & Action -->
                        <div class="pt-4 border-t border-slate-100 dark:border-teal-950/80 flex items-center justify-between gap-2">
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1 truncate max-w-[140px]">
                                <i data-lucide="user" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="truncate"><?php echo htmlspecialchars($poll['creator_name'] ?? 'Community Member'); ?></span>
                            </div>
                            
                            <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo (int)$poll['id']; ?>" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/60 border border-teal-200 dark:border-teal-800/40 transition-colors shrink-0 min-h-[44px]">
                                <span><?php echo ($lifecycle === 'Closed') ? 'Results' : 'Vote Now'; ?></span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- What People Ask: Everyday Scenarios & Honest Vision -->
<section class="py-16 md:py-24 border-b border-slate-200/80 dark:border-teal-950/80 bg-white/40 dark:bg-[#061214]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            
            <!-- Left Column: Story & Vision (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-2">
                        Everyday Use Cases
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight leading-snug">
                        Built for Questions Big and Small
                    </h2>
                </div>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    SecurePoll is designed for anyone who wants to run a quick poll, collect honest feedback, and see what people really think without intrusive trackers or complicated setups.
                </p>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    We're starting with everyday polls and small communities. If the project proves useful and receives meaningful feedback, we may explore additional features for larger groups in the future.
                </p>

                <div class="pt-2">
                    <a href="<?php echo $basePrefix; ?>/about" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 dark:text-teal-400 hover:underline">
                        <span>Read about the project and creators</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Right Column: Real-World Scenarios (7 cols) -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="gamepad-2" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Gaming & Hobbies</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        "Which game should our squad play tonight?" or "Which setup would you choose?" Compare favorites and settle debates easily.
                    </p>
                </div>

                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Projects & Ideas</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        "Whose prototype looks better?" or "Which idea should we build next?" Get quick feedback from friends and fellow creators.
                    </p>
                </div>

                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Everyday Situations</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        "This happened today. Was I in the wrong?" or "What would you do here?" Ask opinions and hear different perspectives.
                    </p>
                </div>

                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Small Groups & Clubs</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Run simple group votes, decide meetup times, or vote on group choices without cumbersome spreadsheet forms.
                    </p>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- Source-Available Note -->
<section class="py-12 bg-teal-500/5 dark:bg-teal-950/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <i data-lucide="scale" class="w-4 h-4 text-teal-500"></i>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Source-Available Project</h4>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                SecurePoll is an independent project with source code on GitHub. Free to explore, use, and share feedback while we learn and build.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="<?php echo $basePrefix; ?>/pricing" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 dark:text-teal-300 hover:underline">
                <span>Early Access Details</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
