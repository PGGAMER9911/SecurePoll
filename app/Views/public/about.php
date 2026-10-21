<?php
$pageTitle = 'About SecurePoll — Creators & Architectural Mission';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-16 md:space-y-24">

    <!-- Editorial Header: Asymmetric Manifesto Layout -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-12">
        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                <span>Origins & Architectural Rationale</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                Institutional Voting Designed for Trust, Not Surveillance
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                SecurePoll was born out of a real problem faced by student unions, faculty committees, and democratic groups: web polling either exposes voter ballot choices to administrators or collapses under campus-wide Wi-Fi networks where everyone shares an IP address.
            </p>
        </div>
    </header>

    <!-- Core Tenets: Editorial Split -->
    <section class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
        <div class="md:col-span-4 space-y-3">
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">01 / Engineering Rationale</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Why We Built SecurePoll Differently
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Conventional survey forms maximize tracking and user profiling. SecurePoll is built around mathematical separation.
            </p>
        </div>

        <div class="md:col-span-8 space-y-6 text-sm text-slate-600 dark:text-slate-300">
            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Decoupled Relational Storage</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Voters must never risk retaliation for their democratic choice. When anonymous mode is active, the database record for a cast ballot contains no foreign key or pointer to the user's account table. A one-way salted HMAC prevents double-voting while rendering retrospective attribution mathematically impossible.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Shared Network & Campus Resilience</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Traditional online voting tools block shared IP addresses, preventing hundreds of students living in university dormitories or staff behind corporate NATs from voting. SecurePoll uses client entropy tokens and signed device identifiers to distinguish unique voters on identical network gateways.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Verifiable Public Architecture</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Democratic systems must be verifiable. The full source code for SecurePoll is published on GitHub under a source-available, non-commercial license so election commissioners and security auditors can verify the tabulation and authentication mechanics.
                </p>
            </div>
        </div>
    </section>

    <!-- The Creators: Verified Dossier Presentation -->
    <section class="space-y-8 pt-10 border-t border-slate-200 dark:border-teal-950/80">
        <div>
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-2">02 / Contributors</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Project Creators & Technical Roles
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                SecurePoll is engineered and maintained by developers committed to security hygiene, architectural simplicity, and democratic integrity.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($creators as $creator): ?>
                <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl p-6 sm:p-8 bg-white dark:bg-[#07181b]/70 flex flex-col justify-between shadow-xs">
                    <div>
                        <!-- Avatar & Header -->
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

                        <!-- Technical Responsibilities Bio -->
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            <?php echo htmlspecialchars($creator['bio']); ?>
                        </p>
                    </div>

                    <!-- Direct Technical Repository Links -->
                    <div class="pt-4 border-t border-slate-100 dark:border-teal-950/80 flex items-center justify-between text-xs">
                        <a href="<?php echo htmlspecialchars($creator['github']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 font-medium transition-colors min-h-[44px]">
                            <i data-lucide="github" class="w-4 h-4"></i>
                            <span>GitHub Profile</span>
                        </a>

                        <a href="<?php echo htmlspecialchars($creator['repo']); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-teal-600 dark:text-teal-400 hover:underline font-medium min-h-[44px]">
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
                <a href="<?php echo $basePrefix; ?>/contact" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                    <span>Contact Team</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
                <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-teal-950 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors min-h-[44px]">
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
