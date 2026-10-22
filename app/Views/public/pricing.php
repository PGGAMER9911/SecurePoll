<?php
$pageTitle = 'Early Access & Availability — SecurePoll';
$pageDescription = 'SecurePoll is currently free during early access while we develop the project and learn from real community use.';
$basePrefix = $basePrefix ?? \app\Helpers\Url::base();
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-16">

    <!-- Header Section -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-10 max-w-2xl">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
            <span>Project Availability</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
            Free During Early Access
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
            SecurePoll is currently free while we develop the project, learn from real usage, and improve the experience. In the future, we may introduce optional paid plans or additional paid features. Nothing about future pricing is guaranteed yet.
        </p>
    </header>

    <!-- Early Access Card -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-2xl bg-white dark:bg-[#07181b]/70 p-6 sm:p-10 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-6 pb-8 border-b border-slate-200 dark:border-teal-950/80">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                    Current Stage
                </span>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-2">
                    Early Access
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    All currently implemented features are free to use while we learn from real feedback.
                </p>
            </div>
            <div class="text-left sm:text-right">
                <div class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">Free</div>
                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">No credit card or payment required.</span>
            </div>
        </div>

        <!-- Available Features List -->
        <div class="py-8 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Currently Available Features</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Poll Creation:</strong> Custom options, binary Yes/No questions, and 1&ndash;5 star ratings.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Visibility Controls:</strong> Public polls, private invitation polls, and unlisted direct permalinks.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Anonymous Voting:</strong> Salted hash tokens help separate voter accounts from ballot selections.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Duplicate Protection:</strong> Basic device tokens to help prevent repeated votes on the same browser.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Email Invitations:</strong> Invite specific email addresses to private polls.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <i data-lucide="check" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 mt-0.5"></i>
                    <span><strong>Participation Metrics:</strong> View vote counts and summary distribution in real time.</span>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="pt-6 border-t border-slate-200 dark:border-teal-950/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Explore available polls or sign up to create your own.
            </span>
            <div class="flex items-center gap-3">
                <a href="<?php echo $basePrefix; ?>/polls" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors min-h-[44px]">
                    <span>Browse Polls</span>
                </a>
                <a href="<?php echo $basePrefix; ?>/polls/create" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                    <span>Create a Poll</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Honest Clarification Note -->
    <div class="border border-slate-200 dark:border-teal-950/80 rounded-xl p-6 bg-slate-50/60 dark:bg-slate-900/40 text-xs sm:text-sm text-slate-600 dark:text-slate-400 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <strong class="text-slate-900 dark:text-white block mb-0.5">Feedback & Questions</strong>
            <span>Have thoughts on how SecurePoll could be improved, or questions about the project? We appreciate hearing from you.</span>
        </div>
        <a href="<?php echo $basePrefix; ?>/contact" class="font-semibold text-teal-600 dark:text-teal-400 hover:underline shrink-0 min-h-[44px] inline-flex items-center">
            Contact Us &rarr;
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
