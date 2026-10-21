<?php
$pageTitle = 'Pricing & Availability — SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 space-y-16">

    <!-- Header Section -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-10 text-center max-w-2xl mx-auto">
        <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest block mb-2">
            Licensing & Availability
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Zero Cost for Academic & Community Governance
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
            SecurePoll was built to serve universities, student councils, and democratic communities. We do not impose artificial voter caps, feature gates, or hidden paywalls.
        </p>
    </header>

    <!-- The Singular True Tier: Institutional & Community Free Tier -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white/70 dark:bg-[#07181b]/70 p-8 sm:p-10 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-4 pb-8 border-b border-slate-200 dark:border-teal-950/80">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                    Active Availability
                </span>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-2">
                    Community & Institutional Tier
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Free forever for non-commercial, academic, and research deployment.
                </p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">$0</div>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">No credit card required. Free forever.</span>
            </div>
        </div>

        <!-- Capability Matrix List -->
        <div class="py-8 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Included Core Capabilities</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Unlimited Elections:</strong> Create Custom, Yes/No, and Star Rating ballots with zero quota limits.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Cryptographic Anonymity:</strong> Salted SHA-256 HMAC decoupling shields voter choices from administrative records.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Flexible Visibility:</strong> Public showcase feeds, private invitation-only ballots, or unlisted permalinks.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Batch CSV Rosters:</strong> Import voter cohorts directly via CSV with automated single-use token issuance.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Device Trust & Anti-Stuffing:</strong> Cryptographic client tokens prevent duplicate votes on shared institutional networks.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Audit Trails & Analytics:</strong> Real-time participation curves, turnout velocity, and administrative change logs.</span>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="pt-6 border-t border-slate-200 dark:border-teal-950/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Ready to deploy your first ballot? Account creation takes 30 seconds.
            </span>
            <a href="<?php echo $basePrefix; ?>/polls/create" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs">
                <span>Create a Poll Now</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <!-- Source Availability Note -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-xl p-6 bg-slate-50/60 dark:bg-slate-900/40 text-xs sm:text-sm text-slate-600 dark:text-slate-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <strong class="text-slate-900 dark:text-white block mb-0.5">Source-Available & Non-Commercial Licence</strong>
            <span>SecurePoll is licensed for non-commercial and academic usage with attribution. For commercial enterprise deployment licensing, please contact our team.</span>
        </div>
        <a href="<?php echo $basePrefix; ?>/contact" class="font-semibold text-teal-600 dark:text-teal-400 hover:underline shrink-0">
            Contact Team &rarr;
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
