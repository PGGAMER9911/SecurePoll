<?php
$pageTitle = 'Pricing & Availability — SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-16">

    <!-- Header Section -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
            <span>Licensing & Availability</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Free for Academic & Community Governance
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
            SecurePoll was created to empower student councils, faculty senates, and democratic communities. We do not impose artificial voter caps, feature paywalls, or commercial subscription tiers.
        </p>
    </header>

    <!-- The Singular True Tier: Institutional & Community Free Tier -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white dark:bg-[#07181b]/70 p-6 sm:p-10 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-6 pb-8 border-b border-slate-200 dark:border-teal-950/80">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                    Community & Academic Availability
                </span>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-2">
                    Institutional Tier
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Free forever for non-commercial, academic, student, and research deployment.
                </p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">$0</div>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">No subscription. Source-available.</span>
            </div>
        </div>

        <!-- Capability Matrix List -->
        <div class="py-8 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Included Core Capabilities</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Unrestricted Poll Creation:</strong> Custom options, binary Yes/No, and 1&ndash;5 star rating scales.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Cryptographic Anonymity:</strong> Salted SHA-256 HMAC decoupling completely isolates voter accounts from ballots.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Visibility Scopes:</strong> Public showcase polls, private invitation-only elections, and unlisted direct permalinks.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Batch CSV Rostering:</strong> Import voter cohorts directly with single-use cryptographic invitation tokens.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Anti-Stuffing Device Trust:</strong> Entropy signature client tokens prevent duplicate votes on shared campus networks.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Immutable Audit Trails:</strong> Timestamped administrative event logs and voter participation metrics.</span>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="pt-6 border-t border-slate-200 dark:border-teal-950/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Ready to launch your institutional election? Sign up takes 30 seconds.
            </span>
            <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                <span>Create a Ballot Now</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <!-- Source Availability Note -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-xl p-6 bg-slate-50/60 dark:bg-slate-900/40 text-xs sm:text-sm text-slate-600 dark:text-slate-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <strong class="text-slate-900 dark:text-white block mb-0.5">Source-Available & Non-Commercial Licence</strong>
            <span>SecurePoll source code is public on GitHub. Non-commercial and academic usage is completely free with author attribution. Commercial SaaS hosting requires written authorization.</span>
        </div>
        <a href="<?php echo $basePrefix; ?>/contact" class="font-semibold text-teal-600 dark:text-teal-400 hover:underline shrink-0 min-h-[44px] inline-flex items-center">
            Contact Developers &rarr;
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
