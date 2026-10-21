<?php
$pageTitle = 'Contact — SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 space-y-16">

    <!-- Header -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-10 text-center max-w-2xl mx-auto">
        <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest block mb-2">
            Direct Communication
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Get in Touch with the SecurePoll Team
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
            For architectural inquiries, security vulnerability disclosures, institutional deployments, or bug reports, contact us through our official verified channels.
        </p>
    </header>

    <!-- Communication Cards Grid -->
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Primary Support Email Dossier -->
        <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white/70 dark:bg-[#07181b]/70 p-8 sm:p-10 shadow-xs">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                    <i data-lucide="mail" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">General & Security Support</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Direct correspondence with the core developers.
                    </p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                All inquiries regarding platform architecture, academic deployment, licensing permissions, and bug reports should be dispatched directly to our verified email address:
            </p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-teal-950/80">
                <div class="flex items-center gap-2 text-sm font-mono font-semibold text-slate-900 dark:text-white">
                    <i data-lucide="at-sign" class="w-4 h-4 text-teal-600 dark:text-teal-400"></i>
                    <span>contact@pgdev.in</span>
                </div>
                <a href="mailto:contact@pgdev.in" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs">
                    <span>Send Message</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="mt-4 flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Direct replies dispatched within 24–48 business hours</span>
            </div>
        </div>

        <!-- GitHub Public Issue Tracker -->
        <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white/70 dark:bg-[#07181b]/70 p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <i data-lucide="github" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Public Issue Tracker</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Track bug fixes, feature requests, and technical discussions</p>
                </div>
            </div>
            <a href="https://github.com/PGGAMER9911/SecurePoll/issues" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-teal-950 transition-colors shrink-0">
                <span>GitHub Issues</span>
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            </a>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
