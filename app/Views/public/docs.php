<?php
$pageTitle = 'Documentation — SecurePoll Guide & Development Journey';
$pageDescription = 'Documentation for SecurePoll: how to create and vote in polls, privacy mechanisms, and the real development journey, challenges, and solutions.';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 md:py-16 w-full min-w-0 overflow-x-hidden">
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-14 items-start w-full min-w-0">

        <!-- Mobile Collapsible Table of Contents (< lg) -->
        <div class="w-full lg:hidden mb-2">
            <details class="border border-slate-200 dark:border-teal-900/60 rounded-xl p-4 bg-white dark:bg-[#07181b] shadow-xs group w-full">
                <summary class="flex items-center justify-between cursor-pointer list-none text-xs font-bold tracking-wider uppercase text-teal-700 dark:text-teal-300 min-h-[44px]">
                    <span class="flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>Documentation Jump Menu</span>
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180"></i>
                </summary>
                <nav class="mt-3 pt-3 border-t border-slate-100 dark:border-teal-950/80 space-y-1 text-xs">
                    <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 block pt-1">User Guide</span>
                    <a href="#about-project" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">1. About the Project</a>
                    <a href="#getting-started" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">2. Getting Started</a>
                    <a href="#creating-a-poll" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">3. Creating a Poll</a>
                    <a href="#polling-types" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">4. Voting Formats</a>
                    <a href="#visibility-models" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">5. Poll Visibility & Access</a>
                    <a href="#privacy-overview" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">6. Privacy & Anonymous Voting</a>
                    
                    <span class="text-[10px] uppercase font-bold text-teal-600 dark:text-teal-400 block pt-3 border-t border-slate-100 dark:border-teal-950/80">Development Journey</span>
                    <a href="#dev-journey" class="block py-2 text-slate-700 dark:text-slate-200 font-semibold hover:text-teal-600 dark:hover:text-teal-400">7. Challenges & Solutions</a>
                    <a href="#limitations" class="block py-2 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">8. Known Limitations & Future Ideas</a>
                </nav>
            </details>
        </div>

        <!-- Sticky Table of Contents Sidebar (Desktop >= lg) -->
        <aside class="hidden lg:block w-64 shrink-0 sticky top-24 space-y-6" aria-label="Table of contents">
            <div>
                <p class="text-xs font-bold tracking-widest text-teal-600 dark:text-teal-400 uppercase mb-3">
                    Guide Outline
                </p>
                <nav class="space-y-0.5 text-xs border-l border-slate-200 dark:border-teal-950/80">
                    <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 pl-3 py-1">User Guide</p>
                    <a href="#about-project" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        1. About the Project
                    </a>
                    <a href="#getting-started" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        2. Getting Started
                    </a>
                    <a href="#creating-a-poll" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        3. Creating a Poll
                    </a>
                    <a href="#polling-types" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        4. Voting Formats
                    </a>
                    <a href="#visibility-models" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        5. Poll Visibility & Access
                    </a>
                    <a href="#privacy-overview" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        6. Privacy & Anonymous Voting
                    </a>

                    <div class="pt-3 pb-1 pl-3 border-t border-slate-100 dark:border-teal-950/80 mt-2">
                        <span class="text-[10px] uppercase font-bold text-teal-600 dark:text-teal-400">Development Journey</span>
                    </div>
                    <a href="#dev-journey" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-700 dark:text-slate-300 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-semibold transition-colors">
                        7. Challenges & Solutions
                    </a>
                    <a href="#limitations" class="block py-1 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        8. Known Limitations & Ideas
                    </a>
                </nav>
            </div>

            <!-- Quick Meta Links -->
            <div class="pt-4 border-t border-slate-200 dark:border-teal-950/80 space-y-2 text-xs">
                <span class="font-bold text-slate-900 dark:text-slate-200 block uppercase tracking-wider text-[11px]">Links & Feedback</span>
                <div class="space-y-1.5 font-medium">
                    <a href="mailto:contact@pgdev.in" class="flex items-center gap-1.5 text-teal-600 dark:text-teal-400 hover:underline">
                        <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>contact@pgdev.in</span>
                    </a>
                    <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i data-lucide="code-2" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>GitHub Repository</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Documentation Main Body -->
        <article class="flex-1 w-full min-w-0 max-w-3xl space-y-12 sm:space-y-16">

            <!-- Title & Introduction -->
            <header class="border-b border-slate-200 dark:border-teal-950/80 pb-6 sm:pb-8">
                <div class="flex items-center gap-2 text-xs font-semibold text-teal-600 dark:text-teal-400 uppercase tracking-widest mb-2 flex-wrap">
                    <span>Documentation & Practical Guide</span>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <span>Early Access</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-3">
                    SecurePoll User Guide & Development Notes
                </h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    This guide covers how SecurePoll works today, how to create and participate in polls, and the real development journey, challenges, and decisions made while building the project.
                </p>
            </header>

            <!-- 1. What is SecurePoll -->
            <section id="about-project" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">01</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        About the Project
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    SecurePoll is an early-stage polling platform built in native PHP and MySQL. It is created for everyday questions: gamers comparing games or gear, friends debating ideas or projects, young people asking for opinions, and small groups or clubs that want a clean, simple place to vote without heavy corporate tracking or confusing setups.
                </p>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    We focus on practical, working features: straightforward poll creation, three voting formats, clear visibility controls, and basic privacy protection through salted hash tokens in anonymous polls.
                </p>
            </section>

            <!-- 2. Getting Started -->
            <section id="getting-started" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">02</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Getting Started
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    You can browse public polls without signing in. To create your own poll or vote in private polls, follow these simple steps:
                </p>

                <ol class="space-y-3 pt-2 border-t border-slate-200 dark:border-teal-950/80">
                    <li class="flex items-start gap-3 pt-2">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">1</span>
                        <div class="min-w-0 flex-1">
                            <strong class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white block">Register an Account</strong>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Visit <a href="<?php echo $basePrefix; ?>/register" class="text-teal-600 dark:text-teal-400 hover:underline">Register</a> and enter your name, email, and password. An Email OTP code is sent to confirm your email.
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 pt-2">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">2</span>
                        <div class="min-w-0 flex-1">
                            <strong class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white block">Log In</strong>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Log in at <a href="<?php echo $basePrefix; ?>/login" class="text-teal-600 dark:text-teal-400 hover:underline">Login</a> to access your personal dashboard and create polls.
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 pt-2">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">3</span>
                        <div class="min-w-0 flex-1">
                            <strong class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white block">Explore or Create</strong>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Explore community polls at <a href="<?php echo $basePrefix; ?>/polls" class="text-teal-600 dark:text-teal-400 hover:underline">/polls</a> or launch your own poll at <a href="<?php echo $basePrefix; ?>/polls/create" class="text-teal-600 dark:text-teal-400 hover:underline">/polls/create</a>.
                            </p>
                        </div>
                    </li>
                </ol>
            </section>

            <!-- 3. Creating a Poll -->
            <section id="creating-a-poll" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">03</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Creating a Poll
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    When creating a poll on <code class="text-xs font-mono px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">/polls/create</code>, you configure several straightforward settings:
                </p>

                <!-- Responsive Table Wrapper -->
                <div class="overflow-x-auto border border-slate-200 dark:border-teal-950/80 rounded-xl my-4 w-full">
                    <table class="w-full text-left text-xs min-w-[500px]">
                        <thead class="bg-slate-100/70 dark:bg-slate-900/60 border-b border-slate-200 dark:border-teal-950/80 font-bold text-slate-800 dark:text-slate-200">
                            <tr>
                                <th class="p-3">Field</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-teal-950/50 text-slate-600 dark:text-slate-300">
                            <tr>
                                <td class="p-3 font-mono font-semibold text-slate-900 dark:text-white">Title</td>
                                <td class="p-3 text-teal-600 dark:text-teal-400">Required text</td>
                                <td class="p-3">The main question or topic you want to ask people about.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-mono font-semibold text-slate-900 dark:text-white">Description</td>
                                <td class="p-3 text-slate-400">Optional text</td>
                                <td class="p-3">Extra context, rules, or background details for voters.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-mono font-semibold text-slate-900 dark:text-white">Voting Type</td>
                                <td class="p-3 text-teal-600 dark:text-teal-400">Selection</td>
                                <td class="p-3">Custom choices, Yes/No, or a 1&ndash;5 star rating scale.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-mono font-semibold text-slate-900 dark:text-white">Visibility</td>
                                <td class="p-3 text-teal-600 dark:text-teal-400">Selection</td>
                                <td class="p-3">Public (catalog), Private (invitation only), or Unlisted (link only).</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-mono font-semibold text-slate-900 dark:text-white">Anonymous</td>
                                <td class="p-3 text-teal-600 dark:text-teal-400">Toggle</td>
                                <td class="p-3">When enabled, votes are stored with salted hash tokens rather than user IDs.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 4. Voting Formats -->
            <section id="polling-types" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">04</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Voting Formats
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    SecurePoll currently supports three voting formats:
                </p>

                <div class="space-y-4 pt-1">
                    <div class="border-l-2 border-teal-500 pl-4 space-y-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Custom Choices (Single or Multiple Choice)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Add custom options (e.g. game titles, project ideas). Single choice lets participants choose one option, while multiple choice allows picking more than one.
                        </p>
                    </div>

                    <div class="border-l-2 border-teal-500 pl-4 space-y-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Binary Yes / No</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            A quick two-way choice for straightforward questions: "Was I in the wrong?", "Should we do this?", or "Agree / Disagree".
                        </p>
                    </div>

                    <div class="border-l-2 border-teal-500 pl-4 space-y-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Rating Scale (1 to 5 Stars)</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            An interactive 5-star rating for rating experiences, builds, movie recommendations, or project prototypes. The app calculates the average score and count distribution.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 5. Visibility Models -->
            <section id="visibility-models" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">05</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Poll Visibility & Access
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    You can control who sees your poll with three visibility levels:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/60 space-y-1">
                        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Public</span>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">Public Directory</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Listed on the public explore page for anyone to discover and vote on.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/60 space-y-1">
                        <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Private</span>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">Invite Only</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Only accessible to users explicitly invited via their registered email address.
                        </p>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/60 space-y-1">
                        <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Unlisted</span>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">Link Only</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Hidden from public feeds. Anyone with the direct link can participate.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 6. Privacy & Anonymous Voting -->
            <section id="privacy-overview" class="scroll-mt-24 space-y-4">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">06</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Privacy & Anonymous Voting
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    When you enable "Anonymous Voting" on a poll, SecurePoll does not store your user account ID alongside your selected option. Instead, it generates a salted hash signature:
                </p>

                <!-- Contained Responsive Code Block -->
                <div class="my-3 rounded-xl overflow-hidden border border-slate-800 bg-[#071317] text-slate-300 text-xs font-mono shadow-xs w-full max-w-full">
                    <div class="px-4 py-2 bg-[#040c0e] border-b border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Vote Storage Mechanism</span>
                        <span class="text-teal-400 font-sans">Salted Hash</span>
                    </div>
                    <pre class="p-3 sm:p-4 overflow-x-auto leading-relaxed text-xs max-w-full"><code><span class="text-slate-500">// Anonymous vote insertion (app/Models/Vote.php)</span>
$voterHash = hash_hmac('sha256', $pollId . ':' . $deviceToken, $appSecretKey);

INSERT INTO votes (poll_id, option_id, user_id, voter_hash, created_at)
VALUES ($pollId, $optionId, NULL, $voterHash, NOW());</code></pre>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    <strong>Honest Note on Privacy:</strong> This salted hash prevents poll creators and direct database viewers from seeing which user account chose which option, while helping stop repeated submissions from the same device. It is a practical application-level privacy measure rather than a military-grade mathematical anonymity guarantee.
                </p>
            </section>

            <!-- ==================================================================== -->
            <!-- 7. Development Journey: Challenges and Solutions -->
            <!-- ==================================================================== -->
            <section id="dev-journey" class="scroll-mt-24 space-y-6 pt-6 border-t-2 border-teal-500/30">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                        <i data-lucide="git-commit" class="w-3.5 h-3.5"></i>
                        <span>Development History</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Development Journey: Challenges & Solutions
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Building SecurePoll has involved practical engineering, debugging real issues, and learning through hands-on troubleshooting. Here are documented challenges encountered during development and how they were solved.
                    </p>
                </div>

                <div class="space-y-6 text-xs sm:text-sm">
                    
                    <!-- Case A: Poll Creation & Voting Modalities -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 space-y-3 shadow-xs">
                        <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="help-circle" class="w-4 h-4 shrink-0"></i>
                            <span>A. Poll Types & Dynamic Choice Handling</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                            Challenge: Handling different input types cleanly without separate database tables
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Problem:</strong> Initially, supporting Custom options, Yes/No questions, and 1–5 star ratings risked creating separate tables and redundant controller logic for each type.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Solution:</strong> We unified voting options into a single <code class="font-mono text-xs px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-teal-700 dark:text-teal-300">poll_options</code> table. When creating a Yes/No poll, the backend automatically seeds "Yes" and "No" options. For Rating polls, it generates 5 numeric options (1 through 5). The voting view (<code class="font-mono text-xs">app/Views/polls/show.php</code>) adapts its UI accordingly: rendering radio cards for Single Choice, checkboxes for Multiple Choice, and interactive star buttons for Ratings.
                        </p>
                    </div>

                    <!-- Case B: Privacy & Access Controls -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 space-y-3 shadow-xs">
                        <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="lock" class="w-4 h-4 shrink-0"></i>
                            <span>B. Private Poll Access & Invitation Validation</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                            Challenge: Preventing unauthorized voting on private polls while keeping guest voting simple
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Problem:</strong> If a poll is marked "Private", users without an invitation should not be able to guess the URL or submit votes by manipulating HTTP POST requests.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Solution:</strong> In <code class="font-mono text-xs">PollController::show()</code> and <code class="font-mono text-xs">PollController::vote()</code>, we check the poll's visibility. If it is Private, the system checks whether the logged-in user is the creator or has an invitation record in <code class="font-mono text-xs">poll_invitations</code> matching their email. If neither condition is met, access is halted with a clear 403 message rather than leaking the ballot options.
                        </p>
                    </div>

                    <!-- Case C: Authentication & Email OTP -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 space-y-3 shadow-xs">
                        <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="mail-check" class="w-4 h-4 shrink-0"></i>
                            <span>C. Email OTP Rate Limiting & Verification Feedback</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                            Challenge: Preventing OTP spam while allowing users to correct mistyped emails
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Problem:</strong> During registration, users need a way to receive an OTP code to confirm email ownership. However, repeated clicking could spam the mail service, while mistyping an email locked users on the verification screen.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Solution:</strong> We introduced a 60-second cooldown timer on resend requests enforced both in the client JavaScript and the PHP session. Additionally, we added an "Edit Email" action so that if a user mistypes their address, they can correct it without restarting the whole signup flow from scratch.
                        </p>
                    </div>

                    <!-- Case D: Interface, Themes & Mobile Accessibility -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 space-y-3 shadow-xs">
                        <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="smartphone" class="w-4 h-4 shrink-0"></i>
                            <span>D. Mobile Layout Overflow & Light Theme Contrast Bug</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                            Challenge: Solving horizontal scrolling on phones and illegible button text in Light mode
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Problem 1 (Contrast):</strong> In <code class="font-mono text-xs">app/Views/polls/show.php</code>, the primary voting button used <code class="font-mono text-xs">text-slate-900 dark:text-white</code> over a dark indigo/teal background. In Light mode, this rendered dark text on a dark button, making it almost impossible to read.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Solution 1:</strong> Replaced the conflicting text class with explicit <code class="font-mono text-xs">text-white font-semibold</code>, guaranteeing strong contrast regardless of active theme.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Problem 2 (Mobile Overflow):</strong> Long code blocks and wide HTML tables previously forced the entire documentation page to scroll horizontally on screens narrower than 400px.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Solution 2:</strong> Added <code class="font-mono text-xs">w-full min-w-0 overflow-x-hidden</code> to parent wrappers, placed tables inside dedicated <code class="font-mono text-xs">overflow-x-auto</code> containers, and set code snippets to horizontal scroll strictly inside their own container without causing page-wide overflow.
                        </p>
                    </div>

                    <!-- Case E: The Settings HTTP 500 Debugging Story -->
                    <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 space-y-3 shadow-xs">
                        <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs uppercase tracking-wider">
                            <i data-lucide="terminal" class="w-4 h-4 shrink-0"></i>
                            <span>E. Troubleshooting the Settings HTTP 500 Error</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                            Investigation: Finding the root cause of an unexpected 500 error on the production VPS
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Investigation Process:</strong> When opening the Settings page on the live deployment (<code class="font-mono text-xs">securepoll.pgdev.in/settings</code>), the browser returned HTTP 500. Rather than applying speculative fixes, we connected to the VPS via SSH, checked the Nginx error logs and PHP error logs, and wrote a reproduction script.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Root Cause:</strong> The production database was missing newly added notification preference columns, and the controller was invoking session validation before the database table was synchronized.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <strong>Fix & Verification:</strong> We safely migrated the database schema, updated the model to supply default values when columns are absent, and wrote an automated test script simulating authenticated requests to verify that Settings returned HTTP 200 reliably.
                        </p>
                    </div>

                </div>
            </section>

            <!-- 8. Known Limitations & Future Ideas -->
            <section id="limitations" class="scroll-mt-24 space-y-4 pt-6 border-t border-slate-200 dark:border-teal-950/80">
                <div class="flex items-baseline gap-2.5">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">08</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Known Limitations & Future Ideas
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-xs sm:text-sm">
                    In keeping with our commitment to transparency, here are current limitations in SecurePoll and ideas we may explore in the future:
                </p>

                <ul class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 pt-1">
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                        <span><strong>Organization Accounts (Coming Soon):</strong> The registration page currently offers Personal accounts. Organization accounts are marked as "Coming Soon" while we design multi-user team permissions.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="info" class="w-4 h-4 text-teal-500 shrink-0 mt-0.5"></i>
                        <span><strong>Device Token Scope:</strong> Duplicate-vote protection uses a cookie-based device token. If a voter clears browser cookies or uses a private browsing window, a new token is generated.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-teal-500 shrink-0 mt-0.5"></i>
                        <span><strong>Real-Time Updates:</strong> Vote tallies update when a page is refreshed or submitted. We may consider WebSockets or SSE for live vote counters in future iterations.</span>
                    </li>
                </ul>
            </section>

        </article>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
