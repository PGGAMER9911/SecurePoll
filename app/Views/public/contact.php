<?php
$pageTitle = 'Contact — SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>
<!-- Contact Section -->
<section class="relative py-20 lg:py-28 overflow-hidden bg-white dark:bg-[#071416] transition-colors duration-200">
    <!-- Subtle Ambient Glow -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[300px] bg-teal-500/5 dark:bg-teal-500/10 blur-[120px] rounded-full pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 mb-4">
                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                <span>Direct Communication</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-4">
                Get in <span class="text-teal-600 dark:text-teal-400">Touch</span>
            </h1>
            <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">
                Have inquiries about SecurePoll, feedback regarding architectural security, or deployment questions? Reach out directly via verified email.
            </p>
        </div>

        <!-- Contact Cards Container -->
        <div class="max-w-xl mx-auto space-y-6">
            
            <!-- Primary Verified Contact Card -->
            <div class="p-8 sm:p-10 rounded-2xl bg-slate-50/80 dark:bg-[#0a1c1e] border border-slate-200 dark:border-teal-900/60 shadow-lg shadow-teal-950/5 dark:shadow-teal-950/20 backdrop-blur-xs text-center transition-all">
                <div class="w-14 h-14 rounded-2xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto mb-5 shadow-xs">
                    <i data-lucide="mail" class="w-7 h-7"></i>
                </div>
                
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">General & Support Inquiries</h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-md mx-auto mb-6">
                    For all inquiries regarding the SecurePoll platform, technical questions, bug reports, and feedback, please email our official address:
                </p>

                <!-- Prominent Mail Link Button -->
                <a href="mailto:contact@pgdev.in" class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl text-base font-semibold text-white bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 shadow-md shadow-teal-600/20 hover:shadow-teal-600/30 transition-all group">
                    <i data-lucide="send" class="w-4 h-4 transition-transform group-hover:translate-x-0.5"></i>
                    <span>contact@pgdev.in</span>
                </a>

                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-teal-950/80 flex items-center justify-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400"></i>
                    <span>Replies typically dispatched within 24–48 hours</span>
                </div>
            </div>

            <!-- Public Repository & Issues Card -->
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-[#061214] border border-slate-200 dark:border-teal-950/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-teal-950/60 text-slate-700 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <i data-lucide="github" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Technical Issues & Contributions</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Track public bug reports and repository discussions</p>
                    </div>
                </div>
                <a href="https://github.com/PGGAMER9911/SecurePoll/issues" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 bg-white dark:bg-teal-950/50 hover:bg-slate-100 dark:hover:bg-teal-900/60 border border-slate-200 dark:border-teal-800/50 transition-colors shrink-0">
                    <span>GitHub Issues</span>
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>

    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
