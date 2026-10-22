<?php
$pageTitle = 'Contact — SecurePoll';
$pageDescription = 'Contact the SecurePoll core development team for institutional deployment, vulnerability reporting, and inquiries.';
$basePrefix = $basePrefix ?? \app\Helpers\Url::base();
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-16">

    <!-- Header -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
            <span>Direct Inquiries</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Contact SecurePoll
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
            For academic deployment questions, security disclosures, technical inquiries, or general support, reach out directly to our verified contact address.
        </p>
    </header>

    <!-- Communication Cards -->
    <div class="max-w-2xl space-y-6">

        <!-- Primary Support Email Card -->
        <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white dark:bg-[#07181b]/70 p-6 sm:p-8 shadow-xs">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                    <i data-lucide="mail" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Official Contact Email</h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Direct correspondence with the core developers.
                    </p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                All platform inquiries, vulnerability reports, and deployment consultations should be addressed to:
            </p>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-teal-950/80">
                <div class="flex items-center gap-2 text-sm font-mono font-semibold text-slate-900 dark:text-white">
                    <i data-lucide="at-sign" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0"></i>
                    <span id="contactEmail" class="select-all">contact@pgdev.in</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="mailto:contact@pgdev.in" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs">
                        <span>Send Email</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- GitHub Public Repository & Issue Tracker -->
        <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white dark:bg-[#07181b]/70 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <i data-lucide="code-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Open Source Repository</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Technical issues, source code inspection, and change history</p>
                </div>
            </div>
            <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-teal-950 transition-colors shrink-0">
                <span>View Repository</span>
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            </a>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
