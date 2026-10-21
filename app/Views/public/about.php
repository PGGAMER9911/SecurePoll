<?php
$pageTitle = 'About SecurePoll — Creators & Philosophy';
ob_start();
?>

<!-- Header Section -->
<section class="py-16 md:py-20 border-b border-slate-200/80 dark:border-teal-950/80 bg-white/40 dark:bg-[#071518]/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Mission & Architecture</span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-slate-900 dark:text-white mt-2 mb-4">
            Democracy without Surveillance
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl mx-auto">
            SecurePoll was created to solve a fundamental institutional dilemma: how to conduct verified digital elections without tracking or deanonymizing the voters who cast the ballots.
        </p>
    </div>
</section>

<!-- Purpose & Philosophy Section -->
<section class="py-16 md:py-20 border-b border-slate-200/80 dark:border-teal-950/80">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2">
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800/40 flex items-center justify-center">
                    <i data-lucide="eye-off" class="w-4 h-4"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Privacy by Default</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Voters should never fear retaliation for their ballot choices. SecurePoll decouples user identification records from cast votes using one-way cryptographic tokens.
                </p>
            </div>

            <div class="space-y-2">
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800/40 flex items-center justify-center">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Strict Integrity</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Preventing ballot stuffing and duplicate voting cannot rely on simplistic IP filtering. Our device trust verification enforces strict one-person-one-vote rules across shared networks.
                </p>
            </div>

            <div class="space-y-2">
                <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800/40 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Hierarchical Scopes</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Designed for real-world organizations: university departments, student councils, company teams, and community cohorts all maintain isolated targeting scopes.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- The Creators Section -->
<section class="py-16 md:py-24 bg-white/40 dark:bg-[#061214]/60">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-xl mx-auto mb-14">
            <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">The Team</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight mt-1 mb-3">
                Built by Developers, Not Marketers
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                SecurePoll is actively developed with focus on architectural correctness, cryptographic discipline, and clean responsive interfaces.
            </p>
        </div>

        <!-- Creator Profile Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <?php foreach ($creators as $creator): ?>
                <div class="rounded-2xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/80 p-7 flex flex-col justify-between shadow-xs hover:border-teal-500/40 transition-all duration-200">
                    <div>
                        <!-- Header with Avatar and Role -->
                        <div class="flex items-start gap-4 mb-5">
                            <div class="relative w-16 h-16 rounded-2xl overflow-hidden border border-slate-200 dark:border-teal-800/60 shrink-0 bg-slate-100 dark:bg-slate-900">
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
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 shrink-0">
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

                        <!-- Factual Bio -->
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                            <?php echo htmlspecialchars($creator['bio']); ?>
                        </p>
                    </div>

                    <!-- Links & Metadata -->
                    <div class="pt-5 border-t border-slate-100 dark:border-teal-950/80 flex items-center justify-between gap-3 text-xs">
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

        <!-- Note on Collaborator Customization -->
        <div class="mt-12 text-center">
            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-md mx-auto">
                Are you a project collaborator or contributor? Additional profile information can be submitted directly via our repository issue tracker.
            </p>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
