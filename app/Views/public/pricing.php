<?php
$pageTitle = 'Pricing & Availability — SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>
<!-- Pricing & Availability Section -->
<section class="relative py-20 lg:py-28 overflow-hidden bg-white dark:bg-[#071416] transition-colors duration-200">
    <!-- Subtle Background Glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-teal-500/5 dark:bg-teal-500/10 blur-[100px] rounded-full pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 mb-4">
                <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                <span>Open Availability</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-4">
                Committed to <span class="text-teal-600 dark:text-teal-400">Open & Free</span> Access
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">
                SecurePoll is provided completely free for individuals, students, academic institutions, and community organizations. No artificial vote caps, hidden paywalls, or gated tiers.
            </p>
        </div>

        <!-- Single Free / Community Plan Card -->
        <div class="max-w-xl mx-auto">
            <div class="relative p-8 sm:p-10 rounded-2xl bg-slate-50/80 dark:bg-[#0a1c1e] border-2 border-teal-500/30 dark:border-teal-500/40 shadow-xl shadow-teal-950/5 dark:shadow-teal-950/30 backdrop-blur-xs transition-all">
                
                <!-- Badge -->
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="inline-block text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-teal-500 text-white">
                            Active Plan
                        </span>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-2">Community & Institutional</h2>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white">$0</div>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Free forever</span>
                    </div>
                </div>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-8 pb-6 border-b border-slate-200 dark:border-teal-950/80">
                    Full access to the complete SecurePoll voting engine. Deploy ballots, invite participants, configure anonymity protocols, and audit real-time results without paying a single cent.
                </p>

                <!-- Included Capabilities List -->
                <div class="space-y-4 mb-8">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-200">What is included</h3>
                    <ul class="space-y-3 text-sm text-slate-700 dark:text-slate-300">
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>Unlimited Polls & Ballots:</strong> Create Custom, Yes/No, and Star Rating polls without limits.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>Cryptographic Voter Anonymity:</strong> Irreversible salted SHA-256 voter hashes preserve complete ballot privacy.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>Multi-Choice & Single-Choice Voting:</strong> Flexible voting methods tailored to your election rules.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>Full Visibility Controls:</strong> Public, Private (invitation-only), and Unlisted share-link modes.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>CSV Batch Invitations:</strong> Invite entire student cohorts or teams in a single batch upload.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </div>
                            <span><strong>Verifiable Audit Log & Real-Time Analytics:</strong> Live breakdown graphs and participation tracking.</span>
                        </li>
                    </ul>
                </div>

                <!-- Call to Action -->
                <div class="pt-2">
                    <a href="<?php echo $basePrefix; ?>/polls/create" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 shadow-md shadow-teal-600/20 transition-all">
                        <span>Get Started for Free</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <p class="text-[11px] text-center text-slate-500 dark:text-slate-400 mt-3">
                        No credit card required. Free non-commercial licence.
                    </p>
                </div>
            </div>
        </div>

        <!-- Licence & Commercial Note Section -->
        <div class="mt-16 max-w-2xl mx-auto p-6 sm:p-8 rounded-2xl bg-slate-50 dark:bg-[#061214] border border-slate-200 dark:border-teal-950/80 text-center">
            <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="file-check" class="w-5 h-5"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Source-Available & Non-Commercial Licence</h3>
            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                SecurePoll is released under a custom source-available non-commercial licence. It may be used, modified, and redistributed freely for personal, academic, research, and non-commercial institutional purposes with mandatory author attribution.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 text-xs">
                <a href="<?php echo $basePrefix; ?>/contact" class="inline-flex items-center gap-1.5 font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                    <span>Contact for Commercial Licensing Inquiries</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                </a>
                <span class="hidden sm:inline-block text-slate-300 dark:text-slate-700">&bull;</span>
                <a href="https://github.com/PGGAMER9911/SecurePoll/blob/main/LICENSE" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Read Full Licence</span>
                </a>
            </div>
        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
