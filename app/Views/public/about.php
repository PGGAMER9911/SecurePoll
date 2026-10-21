<?php
$pageTitle = 'About SecurePoll — Creators & Philosophy';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 space-y-20">

    <!-- Editorial Header: Asymmetric Manifesto Layout -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-12">
        <div class="max-w-3xl">
            <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest block mb-3">
                Mission & Philosophy &bull; Democracy without Surveillance
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
                Institutional Voting Designed for Trust, Not Surveillance
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                SecurePoll emerged from a practical problem in academic and democratic organizations: most web polling forms either link respondent identities directly to their ballot answers or depend on vulnerable, easily manipulated IP counters.
            </p>
        </div>
    </header>

    <!-- Core Tenets: Editorial 2-Column Split (Not 3 Generic Centered Cards) -->
    <section class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <div class="md:col-span-4">
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-2">01 / Principles</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Why We Built SecurePoll Differently
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Traditional commercial polling tools optimize for engagement metrics and tracking. SecurePoll was engineered around mathematical isolation.
            </p>
        </div>

        <div class="md:col-span-8 space-y-6 text-sm text-slate-600 dark:text-slate-300">
            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Decoupled Ballot Records</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Voters must never fear retaliation. When anonymous mode is active, the database record for a cast vote has no relational foreign key to the voter’s account table. Instead, a one-way salted device HMAC prevents duplicate submissions while rendering retrospective attribution computationally infeasible.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Campus & Office Friendly Integrity</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Naïve polling tools block shared IP addresses, preventing students in dorms or staff in university laboratories from voting. SecurePoll uses client entropy tokens to distinguish legitimate independent voters sharing a single institutional gateway.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Open & Verifiable Codebase</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Elections should be auditable. The full source code for SecurePoll is publicly hosted on GitHub under a source-available, non-commercial license so election administrators can independently review the authentication and tabulation logic.
                </p>
            </div>
        </div>
    </section>

    <!-- The Creators: Distinct, Factual Dossier Presentation -->
    <section class="space-y-8 pt-8 border-t border-slate-200 dark:border-teal-950/80">
        <div>
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-2">02 / Engineering Team</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Project Creators & Technical Contributors
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                SecurePoll is built and maintained by developers focused on architectural correctness, security hygiene, and minimalist user experience.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($creators as $creator): ?>
                <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl p-6 sm:p-7 bg-white/70 dark:bg-[#07181b]/70 flex flex-col justify-between">
                    <div>
                        <!-- Avatar & Identity Header -->
                        <div class="flex items-start gap-4 mb-5">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-teal-800/60 bg-slate-100 dark:bg-slate-900 shrink-0">
                                <img src="<?php echo htmlspecialchars($creator['avatar']); ?>" 
                                     alt="<?php echo htmlspecialchars($creator['name']); ?>" 
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($creator['fallback_avatar']); ?>';">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white truncate">
                                        <?php echo htmlspecialchars($creator['name']); ?>
                                    </h3>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                                        <?php echo htmlspecialchars($creator['badge']); ?>
                                    </span>
                                </div>
                                <p class="text-xs font-semibold text-teal-600 dark:text-teal-400 mb-0.5">
                                    <?php echo htmlspecialchars($creator['role']); ?>
                                </p>
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">
                                    @<?php echo htmlspecialchars($creator['handle']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Factual Technical Responsibilities Bio -->
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            <?php echo htmlspecialchars($creator['bio']); ?>
                        </p>
                    </div>

                    <!-- Direct Technical Repository Links -->
                    <div class="pt-4 border-t border-slate-100 dark:border-teal-950/80 flex items-center justify-between text-xs">
                        <a href="<?php echo htmlspecialchars($creator['github']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 font-medium transition-colors">
                            <i data-lucide="github" class="w-4 h-4"></i>
                            <span>GitHub Profile</span>
                        </a>

                        <a href="<?php echo htmlspecialchars($creator['repo']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-teal-600 dark:text-teal-400 hover:underline font-medium">
                            <span>Repository</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Source Availability & Non-Commercial License Section -->
    <section class="border-t border-slate-200 dark:border-teal-950/80 pt-10">
        <div class="p-6 sm:p-8 rounded-2xl bg-slate-50/70 dark:bg-[#061214]/80 border border-slate-200 dark:border-teal-950/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-1 max-w-xl">
                <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">Governance & Licensing</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Source-Available for Community Benefit</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    SecurePoll is released under a custom source-available non-commercial license. You are free to inspect, audit, host, and adapt the software for student unions, non-profit institutions, and research endeavors with mandatory author attribution.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="<?php echo $basePrefix; ?>/contact" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs">
                    <span>Contact Team</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
                <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-teal-950 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="github" class="w-3.5 h-3.5"></i>
                    <span>Repository</span>
                </a>
            </div>
        </div>
    </section>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
