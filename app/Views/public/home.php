<?php
use app\Services\PollService;

$pageTitle = 'SecurePoll — Privacy-Centric Institutional Voting';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<!-- Hero Section with Dark Teal Abstract Atmosphere -->
<section class="relative overflow-hidden pt-16 pb-24 md:pt-24 md:pb-32 border-b border-slate-200/80 dark:border-teal-950/80">
    <!-- Background Image with Moody Gradient & Vignette Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="<?php echo $basePrefix; ?>/images/hero-bg.jpg" alt="" class="w-full h-full object-cover object-center filter brightness-50 contrast-125 opacity-25 dark:opacity-40 select-none pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-50/90 via-slate-50/70 to-slate-50 dark:from-[#071416]/80 dark:via-[#071416]/90 dark:to-[#071416]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-teal-500/10 via-transparent to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Top Pill Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 dark:bg-teal-500/15 text-teal-700 dark:text-teal-300 border border-teal-500/30 mb-6 backdrop-blur-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
            <span>Cryptographically Audited Digital Balloting</span>
        </div>

        <!-- Headline -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-slate-900 dark:text-white leading-[1.15] max-w-4xl mx-auto mb-6">
            Privacy-first voting for institutions that demand <span class="text-teal-600 dark:text-teal-400">absolute integrity</span>.
        </h1>

        <!-- Product-Specific Subhead -->
        <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed mb-10">
            SecurePoll pairs mathematical voter anonymity with tamper-evident verifiability. Purpose-built for universities, student bodies, departments, and communities conducting trusted digital elections.
        </p>

        <!-- Call To Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md mx-auto">
            <a href="<?php echo $basePrefix; ?>/polls" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-md shadow-teal-600/20 hover:shadow-teal-600/30 transition-all">
                <span>Explore Active Polls</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <a href="<?php echo $basePrefix; ?>/docs" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 bg-white/80 dark:bg-teal-950/40 hover:bg-white dark:hover:bg-teal-950/70 border border-slate-200 dark:border-teal-800/60 shadow-xs transition-all">
                <i data-lucide="book-open" class="w-4 h-4 text-teal-500"></i>
                <span>Read Documentation</span>
            </a>
        </div>

        <!-- Trust Features Strip -->
        <div class="mt-16 pt-8 border-t border-slate-200/80 dark:border-teal-950/80 grid grid-cols-2 md:grid-cols-4 gap-4 text-left">
            <div class="p-3 rounded-lg bg-white/40 dark:bg-teal-950/20 border border-slate-200/60 dark:border-teal-900/30">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-slate-200 mb-1">
                    <i data-lucide="shield-check" class="w-4 h-4 text-teal-500 shrink-0"></i>
                    <span>Zero Deanonymization</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                    Salted cryptographic hashes decouple voter accounts from submitted ballots.
                </p>
            </div>
            <div class="p-3 rounded-lg bg-white/40 dark:bg-teal-950/20 border border-slate-200/60 dark:border-teal-900/30">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-slate-200 mb-1">
                    <i data-lucide="layout-grid" class="w-4 h-4 text-teal-500 shrink-0"></i>
                    <span>Multi-Mode Ballots</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                    Single-choice, multiple-choice, rating scale (1-5), and binary Yes/No ballots.
                </p>
            </div>
            <div class="p-3 rounded-lg bg-white/40 dark:bg-teal-950/20 border border-slate-200/60 dark:border-teal-900/30">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-slate-200 mb-1">
                    <i data-lucide="lock" class="w-4 h-4 text-teal-500 shrink-0"></i>
                    <span>Bound Invitations</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                    Expiring, single-use cryptographically bound tokens for private voter rosters.
                </p>
            </div>
            <div class="p-3 rounded-lg bg-white/40 dark:bg-teal-950/20 border border-slate-200/60 dark:border-teal-900/30">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-900 dark:text-slate-200 mb-1">
                    <i data-lucide="cpu" class="w-4 h-4 text-teal-500 shrink-0"></i>
                    <span>Device Trust Engine</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal">
                    Fingerprint anomaly detection and persistent device cookie duplicate prevention.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Architectural Integrity & System Tenets (Asymmetric Editorial Layout) -->
<section class="py-20 md:py-28 border-b border-slate-200/80 dark:border-teal-950/80 bg-white/40 dark:bg-[#061214]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left Column: Editorial Narrative (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Engineering Principles</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mt-2 leading-snug">
                        The distinction between informal surveys and verified elections.
                    </h2>
                </div>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Most web polling tools rely on IP address tracking or require users to surrender ballot privacy to prevent duplicate voting. On institutional campuses, university dorms, and enterprise proxies, raw IP logging fails immediately.
                </p>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    SecurePoll was designed from the ground up to reconcile two conflicting democratic requirements: <strong class="text-slate-900 dark:text-white font-semibold">absolute voter anonymity</strong> and <strong class="text-slate-900 dark:text-white font-semibold">strict one-person-one-vote enforcement</strong>.
                </p>

                <div class="pt-4 flex items-center gap-3">
                    <a href="<?php echo $basePrefix; ?>/about" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 dark:text-teal-400 hover:underline">
                        <span>Read our architectural philosophy</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Right Column: Structured Verification Matrix (7 cols) -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Tenet 1: Salted voter_hash -->
                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="fingerprint" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Cryptographic Anonymity</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Votes are recorded with a one-way salted SHA-256 <code class="text-[11px] font-mono px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-300">voter_hash</code>. Neither system administrators nor database dumps can link an individual voter to their selected candidate or ballot choice.
                    </p>
                </div>

                <!-- Tenet 2: Device Trust Anchoring -->
                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Anti-Stuffing Device Trust</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Prevents ballot stuffing across shared institutional networks using cryptographically signed device identifiers and browser fingerprint verification.
                    </p>
                </div>

                <!-- Tenet 3: Flexible Ballot Rules -->
                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="sliders" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Governed Ballot Modes</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Supports single-choice, multiple-choice, binary Yes/No, and 1–5 star rating scales. Quorum thresholds and scheduled expiration automate poll closing.
                    </p>
                </div>

                <!-- Tenet 4: Tamper-Evident Audit Trail -->
                <div class="p-5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/80 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                            <i data-lucide="file-check-2" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tamper-Evident Audit Log</h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Administrative alterations, option edits, and roster modifications are logged to immutable audit tables with timestamping and actor records.
                    </p>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- Active Public Polls Showcase Section -->
<section id="public-polls" class="py-20 md:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
            <div>
                <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Live Public Participation</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mt-1">
                    Featured Public Polls
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Live ballots currently open for public participation across the SecurePoll community.
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
            <div class="p-12 sm:p-16 rounded-2xl border-2 border-dashed border-slate-200 dark:border-teal-950 bg-white/50 dark:bg-[#07181b]/30 text-center max-w-lg mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800/40 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1.5">No Active Public Polls Right Now</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                    All current public polls have ended or new polls are in scheduled preparation. Sign in to create the next community ballot.
                </p>
                <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-all shadow-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Create a Public Poll</span>
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
                        default => 'Ballot'
                    };
                    ?>
                    <div class="group relative rounded-2xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/70 p-6 flex flex-col justify-between hover:border-teal-500/40 hover:shadow-lg dark:hover:shadow-teal-950/50 transition-all duration-200">
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
                                    Community ballot open for public voting.
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Footer Meta & Action -->
                        <div class="pt-4 border-t border-slate-100 dark:border-teal-950/80 flex items-center justify-between gap-2">
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1 truncate">
                                <i data-lucide="user" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="truncate"><?php echo htmlspecialchars($poll['creator_name'] ?? 'Community Creator'); ?></span>
                            </div>
                            
                            <a href="<?php echo $basePrefix; ?>/polls/show?id=<?php echo (int)$poll['id']; ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 hover:bg-teal-100 dark:hover:bg-teal-900/60 border border-teal-200 dark:border-teal-800/40 transition-colors shrink-0">
                                <span><?php echo ($lifecycle === 'Closed') ? 'View Results' : 'Vote Now'; ?></span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Source-Available Non-Commercial Notice Banner -->
<section class="py-12 border-t border-slate-200/80 dark:border-teal-950/80 bg-teal-500/5 dark:bg-teal-950/20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-teal-500"></i>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Source-Available & Attribution Governed</h4>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed">
                SecurePoll is released under a custom source-available license. Free for education, personal evaluation, and research. Commercial redistribution or SaaS hosting requires written authorization.
            </p>
        </div>
        <a href="<?php echo $basePrefix; ?>/pricing" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-700 dark:text-teal-300 hover:underline shrink-0">
            <span>Licensing Details</span>
            <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
