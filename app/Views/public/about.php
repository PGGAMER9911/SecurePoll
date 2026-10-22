<?php
$pageTitle = 'About SecurePoll — The Project Story & Creators';
$pageDescription = 'The story behind SecurePoll: an early-stage polling project built for everyday questions, friendly debates, and honest community feedback.';
$basePrefix = $basePrefix ?? \app\Helpers\Url::base();
ob_start();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-16 md:space-y-24">

    <!-- Header Section -->
    <header class="border-b border-slate-200 dark:border-teal-950/80 pb-12">
        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                <span>Project Story & Progress</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                Simple Polls for Everyday Questions
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                SecurePoll is an early-stage project started with a straightforward goal: give people a clean, simple place to ask questions, compare opinions, and run polls without heavy trackers or corporate clutter.
            </p>
        </div>
    </header>

    <!-- Core Tenets: Real Development Story -->
    <section class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start">
        <div class="md:col-span-4 space-y-3">
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block">01 / Our Current Stage</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Starting Small, Learning Step by Step
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                We're testing, fixing issues, and building features incrementally based on real practical use.
            </p>
        </div>

        <div class="md:col-span-8 space-y-6 text-sm text-slate-600 dark:text-slate-300">
            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Who It's For Today</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Our initial audience is everyday users: gamers debating which game or loadout is best, friends comparing creative projects and ideas, young people asking for outside opinions on real-life situations, and small communities or clubs making group decisions.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">What Has Actually Been Built</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    We've focused practical development on: custom choice polls, binary Yes/No questions, 1&ndash;5 star ratings, poll visibility scopes (Public, Private with email invitations, and Unlisted permalinks), account registration with Email OTP verification, salted hash tokens for anonymous polls, and a responsive interface with light and dark themes.
                </p>
            </div>

            <div class="border-l-2 border-teal-500/40 pl-5 space-y-1.5">
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Looking Ahead Honestly</h3>
                <p class="leading-relaxed text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    We don't claim to be an enterprise platform or an independently audited national voting system. We're starting with everyday polls. If the application proves genuinely useful and community feedback warrants it, we may explore features for student councils, clubs, or larger organizations in the future.
                </p>
            </div>
        </div>
    </section>

    <!-- The Creators: Verified Dossier Presentation -->
    <section class="space-y-8 pt-10 border-t border-slate-200 dark:border-teal-950/80">
        <div>
            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider block mb-2">02 / Contributors</span>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Project Creators & Roles
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                SecurePoll is developed by creators learning through practical software development, focusing on clear code, usability, and respectful privacy.
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
                                <a href="<?php echo htmlspecialchars($creator['github']); ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-slate-400 dark:text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 font-mono transition-colors">
                                    @<?php echo htmlspecialchars($creator['handle']); ?>
                                </a>
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
                            <i data-lucide="code-2" class="w-4 h-4"></i>
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
                <div class="flex items-center gap-1.5 mb-1"><i data-lucide="scale" class="w-4 h-4 text-teal-600 dark:text-teal-400"></i><span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Source Available</span></div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Built in the Open</h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    SecurePoll's source code is public on GitHub. We welcome feedback, code inspection, bug reports, and suggestions from fellow developers and users.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="<?php echo $basePrefix; ?>/contact" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs min-h-[44px]">
                    <span>Contact Team</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
                <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-teal-950 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors min-h-[44px]">
                    <i data-lucide="code-2" class="w-3.5 h-3.5"></i>
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
