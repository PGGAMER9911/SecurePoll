<?php
$pageTitle = 'Documentation — SecurePoll Voting Platform';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">

        <!-- Mobile Collapsible Table of Contents (< lg) -->
        <div class="w-full lg:hidden mb-4">
            <details class="border border-slate-200 dark:border-teal-900/60 rounded-xl p-4 bg-white dark:bg-[#07181b] shadow-xs group">
                <summary class="flex items-center justify-between cursor-pointer list-none text-xs font-bold tracking-wider uppercase text-teal-700 dark:text-teal-300">
                    <span class="flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4"></i>
                        <span>Manual Quick Jump (9 Sections)</span>
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200 group-open:rotate-180"></i>
                </summary>
                <nav class="mt-3 pt-3 border-t border-slate-100 dark:border-teal-950/80 space-y-2 text-xs">
                    <a href="#what-is-securepoll" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">1. Architecture & Principles</a>
                    <a href="#getting-started" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">2. Getting Started</a>
                    <a href="#creating-a-poll" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">3. Poll Creation & Parameters</a>
                    <a href="#balloting-types" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">4. Balloting Modalities</a>
                    <a href="#visibility-models" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">5. Visibility & Access Models</a>
                    <a href="#scheduling-quorums" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">6. Lifecycle & Quorums</a>
                    <a href="#cryptographic-privacy" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">7. Cryptographic Anonymity</a>
                    <a href="#managing-sharing" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">8. Administration & Rosters</a>
                    <a href="#troubleshooting" class="block py-1 text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400">9. Diagnostics & Recovery</a>
                </nav>
            </details>
        </div>

        <!-- Sticky Table of Contents Sidebar (Desktop >= lg) -->
        <aside class="hidden lg:block w-64 shrink-0 sticky top-24 space-y-8" aria-label="Table of contents">
            <div>
                <p class="text-xs font-bold tracking-widest text-teal-600 dark:text-teal-400 uppercase mb-3">
                    Manual Outline
                </p>
                <nav class="space-y-1 text-xs border-l border-slate-200 dark:border-teal-950/80">
                    <a href="#what-is-securepoll" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        1. Architecture & Principles
                    </a>
                    <a href="#getting-started" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        2. Getting Started
                    </a>
                    <a href="#creating-a-poll" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        3. Poll Creation & Parameters
                    </a>
                    <a href="#balloting-types" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        4. Balloting Modalities
                    </a>
                    <a href="#visibility-models" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        5. Visibility & Access Models
                    </a>
                    <a href="#scheduling-quorums" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        6. Lifecycle & Quorums
                    </a>
                    <a href="#cryptographic-privacy" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        7. Cryptographic Anonymity
                    </a>
                    <a href="#managing-sharing" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        8. Administration & Rosters
                    </a>
                    <a href="#troubleshooting" class="block py-1.5 pl-3 -ml-px border-l-2 border-transparent text-slate-600 dark:text-slate-400 hover:border-teal-500 hover:text-teal-600 dark:hover:text-teal-300 font-medium transition-colors">
                        9. Diagnostics & Recovery
                    </a>
                </nav>
            </div>

            <!-- Quick Reference Meta Block -->
            <div class="pt-6 border-t border-slate-200 dark:border-teal-950/80 space-y-3 text-xs">
                <span class="font-bold text-slate-900 dark:text-slate-200 block uppercase tracking-wider text-[11px]">Developer Inquiries</span>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    Have feedback or found an edge case in the voting engine?
                </p>
                <div class="space-y-1.5 font-medium">
                    <a href="mailto:contact@pgdev.in" class="flex items-center gap-1.5 text-teal-600 dark:text-teal-400 hover:underline">
                        <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>contact@pgdev.in</span>
                    </a>
                    <a href="https://github.com/PGGAMER9911/SecurePoll/issues" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i data-lucide="github" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>GitHub Issue Tracker</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Documentation Main Body: Clean Technical Editorial Layout -->
        <article class="flex-1 min-w-0 max-w-3xl space-y-16">

            <!-- Title & Lead -->
            <header class="border-b border-slate-200 dark:border-teal-950/80 pb-8">
                <div class="flex items-center gap-2 text-xs font-semibold text-teal-600 dark:text-teal-400 uppercase tracking-widest mb-2">
                    <span>Technical Reference</span>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <span>Version 1.0</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
                    SecurePoll Technical Manual
                </h1>
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                    A comprehensive guide to deploying, configuring, and verifying elections on SecurePoll. Explains cryptographic separation, anti-stuffing constraints, balloting modalities, and voter governance.
                </p>
            </header>

            <!-- 1. What is SecurePoll -->
            <section id="what-is-securepoll" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">01</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Architecture & Core Principles
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    SecurePoll is an enterprise digital voting engine engineered in native PHP and MySQL. Designed for universities, student bodies, community groups, and democratic organizations, it addresses a fundamental weakness of web-based polling: survey forms often conflate voter identity with ballot selections or rely on easily spoofed IP addresses.
                </p>

                <!-- Clean Editorial Definition List (No Clunky Cards) -->
                <dl class="space-y-4 pt-2 border-l-2 border-teal-500/30 pl-4 sm:pl-6 my-6">
                    <div>
                        <dt class="font-bold text-slate-900 dark:text-white text-sm">Decoupled Cryptographic Anonymity</dt>
                        <dd class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                            Individual vote choices are stored without foreign keys to voter accounts. Salted cryptographic device hashes guarantee that even database administrators inspecting raw tables cannot connect a specific voter to their chosen ballot option.
                        </dd>
                    </div>
                    <div>
                        <dt class="font-bold text-slate-900 dark:text-white text-sm">Anti-Stuffing Device Trust</dt>
                        <dd class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                            Rather than blocking shared IP addresses (which breaks voting on university campuses or corporate offices), SecurePoll anchors participation to cryptographic device tokens and entropy signatures to prevent multiple submissions from the same client.
                        </dd>
                    </div>
                    <div>
                        <dt class="font-bold text-slate-900 dark:text-white text-sm">Scoped Multi-Tenant Governance</dt>
                        <dd class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                            Ballots can be partitioned by college, department, or cohort rosters with single-use cryptographic token validation.
                        </dd>
                    </div>
                    <div>
                        <dt class="font-bold text-slate-900 dark:text-white text-sm">Verifiable Audit Trails</dt>
                        <dd class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                            Lifecycle state transitions (poll publication, roster modification, closure, results publication) are permanently recorded with timestamps and actor metadata.
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- 2. Getting Started -->
            <section id="getting-started" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">02</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Getting Started
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Participation rules depend on the poll's visibility and privacy settings. Anyone can vote in open public polls without authentication if the creator allows guest participation. Creating ballots and managing rosters requires an active, verified account.
                </p>

                <!-- Clean Sequential Steps -->
                <ol class="space-y-4 pt-2 border-t border-slate-200 dark:border-teal-950/80">
                    <li class="flex items-start gap-3.5 pt-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <strong class="text-sm font-semibold text-slate-900 dark:text-white block">Create a Personal Account</strong>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Visit <a href="<?php echo $basePrefix; ?>/register" class="text-teal-600 dark:text-teal-400 hover:underline">Register</a> and submit your full name, email address, and password. A 6-digit one-time passcode (OTP) will be dispatched to confirm address ownership.
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3.5 pt-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <strong class="text-sm font-semibold text-slate-900 dark:text-white block">Device Trust & Session Authentication</strong>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Sign in at <a href="<?php echo $basePrefix; ?>/login" class="text-teal-600 dark:text-teal-400 hover:underline">Login</a>. If you authenticate from a new browser or device, SecurePoll prompts for email verification before issuing session tokens.
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3.5 pt-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <strong class="text-sm font-semibold text-slate-900 dark:text-white block">Access the Dashboard</strong>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-0.5">
                                Your central control room is located at <code class="text-xs font-mono px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">/dashboard</code>. Here you monitor your active ballots, invitations, and turnout analytics.
                            </p>
                        </div>
                    </li>
                </ol>
            </section>

            <!-- 3. Creating a Poll -->
            <section id="creating-a-poll" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">03</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Poll Creation & Configuration Parameters
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Authenticated creators can deploy ballots via <code class="text-xs font-mono px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">/polls/create</code>. Each ballot configuration supports specific rules:
                </p>

                <!-- Parameter Table -->
                <div class="overflow-x-auto border border-slate-200 dark:border-teal-950/80 rounded-xl my-4">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-100/70 dark:bg-slate-900/60 border-b border-slate-200 dark:border-teal-950/80 font-bold text-slate-800 dark:text-slate-200">
                            <tr>
                                <th class="p-3 sm:p-4">Parameter</th>
                                <th class="p-3 sm:p-4">Type</th>
                                <th class="p-3 sm:p-4">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-teal-950/50 text-slate-600 dark:text-slate-300">
                            <tr>
                                <td class="p-3 sm:p-4 font-mono font-semibold text-slate-900 dark:text-white text-xs">title</td>
                                <td class="p-3 sm:p-4 text-xs font-mono text-teal-600 dark:text-teal-400">String (required)</td>
                                <td class="p-3 sm:p-4">The core resolution, candidate ballot question, or voting prompt. Max 255 characters.</td>
                            </tr>
                            <tr>
                                <td class="p-3 sm:p-4 font-mono font-semibold text-slate-900 dark:text-white text-xs">description</td>
                                <td class="p-3 sm:p-4 text-xs font-mono text-slate-400">Text (optional)</td>
                                <td class="p-3 sm:p-4">Background information, candidate qualifications, or voting instructions.</td>
                            </tr>
                            <tr>
                                <td class="p-3 sm:p-4 font-mono font-semibold text-slate-900 dark:text-white text-xs">voting_type</td>
                                <td class="p-3 sm:p-4 text-xs font-mono text-teal-600 dark:text-teal-400">Enum (required)</td>
                                <td class="p-3 sm:p-4"><code class="font-mono text-xs">custom</code>, <code class="font-mono text-xs">yes_no</code>, or <code class="font-mono text-xs">rating</code>. Governs input UI and tally calculation.</td>
                            </tr>
                            <tr>
                                <td class="p-3 sm:p-4 font-mono font-semibold text-slate-900 dark:text-white text-xs">visibility</td>
                                <td class="p-3 sm:p-4 text-xs font-mono text-teal-600 dark:text-teal-400">Enum (required)</td>
                                <td class="p-3 sm:p-4"><code class="font-mono text-xs">public</code>, <code class="font-mono text-xs">private</code>, or <code class="font-mono text-xs">unlisted</code>. Governs indexing and access permissions.</td>
                            </tr>
                            <tr>
                                <td class="p-3 sm:p-4 font-mono font-semibold text-slate-900 dark:text-white text-xs">is_anonymous</td>
                                <td class="p-3 sm:p-4 text-xs font-mono text-teal-600 dark:text-teal-400">Boolean</td>
                                <td class="p-3 sm:p-4">When enabled (default), strips user account foreign keys and records one-way salted hashes.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 4. Balloting Modalities -->
            <section id="balloting-types" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">04</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Supported Balloting Modalities
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    SecurePoll supports three specialized voting methods designed to match different governance contexts:
                </p>

                <div class="space-y-6 pt-2">
                    <div class="border-l-2 border-teal-500 pl-4 sm:pl-5 space-y-1.5">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Custom Candidate Options</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-normal">Single Choice & Multiple Choice Ballot Formats</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Configure arbitrary options (e.g. candidate names, initiative alternatives). When configured for single choice, radio selection enforces exactly one option. When configured for multiple choice, voters can select up to the maximum specified threshold.
                        </p>
                    </div>

                    <div class="border-l-2 border-teal-500 pl-4 sm:pl-5 space-y-1.5">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Yes / No / Abstain</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-normal">Parliamentary Motions</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Structured for legislative and governance motions where participants vote in favor, against, or formally abstain. Abstentions contribute toward the total quorum threshold without skewing approval percentages.
                        </p>
                    </div>

                    <div class="border-l-2 border-teal-500 pl-4 sm:pl-5 space-y-1.5">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Rating Scale (1–5 Stars)</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-normal">Metric Evaluations</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Presents an interactive 5-star evaluation scale. Suitable for performance reviews, campus facility satisfaction surveys, and priority ranking. The reporting engine automatically computes the arithmetic mean, median, and distribution histogram.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 5. Visibility Models -->
            <section id="visibility-models" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">05</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Visibility & Access Models
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Access is governed by three strictly enforced visibility scopes:
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <div class="space-y-2 border-t-2 border-emerald-500 pt-3">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Public</span>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Community Showcase</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Indexed on public feeds and searchable by all users. Suitable for open campus elections, public community consultations, and public interest questions.
                        </p>
                    </div>

                    <div class="space-y-2 border-t-2 border-amber-500 pt-3">
                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Private</span>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Roster Restricted</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Invisible to outside traffic. Access requires a single-use invitation token or verified membership in an authorized cohort. Direct URL access without a valid token returns an authorization challenge.
                        </p>
                    </div>

                    <div class="space-y-2 border-t-2 border-indigo-500 pt-3">
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Unlisted</span>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Direct Link Only</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Excluded from public catalog feeds and search engines. Only individuals possessing the exact permalink can view and participate.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 6. Lifecycle & Quorums -->
            <section id="scheduling-quorums" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">06</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Ballot Lifecycle, Scheduling & Quorum Limits
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Ballots transition through distinct lifecycle states based on scheduled timeframes and voter turnout caps:
                </p>

                <div class="space-y-3 pt-2 text-xs sm:text-sm">
                    <div class="p-4 rounded-lg bg-slate-100/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-teal-950/60">
                        <span class="font-bold text-amber-600 dark:text-amber-400 block mb-1">Upcoming (<code class="font-mono text-xs">starts_at > NOW()</code>)</span>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            The poll is visible to eligible participants, but ballot submission controls are locked until the start timestamp is reached. An active countdown timer informs voters when submissions open.
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-slate-100/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-teal-950/60">
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 block mb-1">Active (<code class="font-mono text-xs">starts_at <= NOW() AND (ends_at IS NULL OR ends_at > NOW())</code>)</span>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Submissions are accepted. If a <code class="font-mono">max_voters</code> quorum cap was defined, the system monitors turnout in real time and automatically seals the poll upon receiving the final permitted ballot.
                        </p>
                    </div>

                    <div class="p-4 rounded-lg bg-slate-100/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-teal-950/60">
                        <span class="font-bold text-slate-600 dark:text-slate-400 block mb-1">Closed (<code class="font-mono text-xs">ends_at <= NOW() OR vote_count >= max_voters</code>)</span>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Submissions are permanently disabled. Results are rendered according to the poll's result visibility preference (instant release vs creator-controlled publication).
                        </p>
                    </div>
                </div>
            </section>

            <!-- 7. Cryptographic Anonymity Architecture -->
            <section id="cryptographic-privacy" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">07</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Cryptographic Anonymity & Anti-Tamper Pipeline
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    SecurePoll avoids recording voter identity alongside ballot selections. When an anonymous ballot is cast, the application executes a mathematical isolation routine:
                </p>

                <!-- Technical Code Block -->
                <div class="my-4 rounded-xl overflow-hidden border border-slate-800 bg-[#071317] text-slate-300 text-xs font-mono shadow-sm">
                    <div class="px-4 py-2 bg-[#040c0e] border-b border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Anonymization Pipeline — app/Controllers/VoteController.php</span>
                        <span class="text-teal-400">SHA-256 One-Way Salted Hash</span>
                    </div>
                    <pre class="p-4 overflow-x-auto leading-relaxed"><code><span class="text-slate-500">// 1. Generate client device entropy if token is absent</span>
$deviceToken = Cookie::get('sp_device_token') ?? Security::generateEntropyToken();

<span class="text-slate-500">// 2. Compute non-reversible voter signature</span>
$voterHash = hash_hmac(
    'sha256', 
    $pollId . ':' . $deviceToken . ':' . $userAgentFingerprint, 
    $applicationAppKey
);

<span class="text-slate-500">// 3. Store vote with NULL user_id and atomic unique constraint</span>
INSERT INTO votes (poll_id, option_id, user_id, voter_hash, created_at)
VALUES ($pollId, $optionId, NULL, $voterHash, NOW());</code></pre>
                </div>

                <div class="border-l-2 border-teal-500 pl-4 py-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <strong>Integrity Guarantee:</strong> Even if an unauthorized party obtains complete database backups, the one-way HMAC hash cannot be reversed to discover who voted for which option. However, if that same device attempts to vote again, MySQL detects an atomic <code class="font-mono text-xs">UNIQUE(poll_id, voter_hash)</code> constraint violation and rejects the submission immediately.
                </div>
            </section>

            <!-- 8. Administration & Rosters -->
            <section id="managing-sharing" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">08</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Administration, Rosters & Live Analytics
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Creators manage active elections through the dedicated administration console (<code class="text-xs font-mono px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">/polls/manage?id=...</code>):
                </p>

                <ul class="space-y-3 pt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="upload" class="w-4 h-4 text-teal-500 shrink-0 mt-0.5"></i>
                        <span><strong>Batch CSV Voter Import:</strong> Upload clean CSV rosters containing email addresses and student/employee IDs. The system creates individual single-use invitation tokens and stages delivery in the background email queue.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-teal-500 shrink-0 mt-0.5"></i>
                        <span><strong>Turnout Velocity & Analytics:</strong> Track voting turnout percentage against total registered voters and monitor participation velocity over time.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-teal-500 shrink-0 mt-0.5"></i>
                        <span><strong>Emergency Administrative Overrides:</strong> Authors can pause, archive, or immediately close a ballot if irregular activity or unauthorized disclosure is identified.</span>
                    </li>
                </ul>
            </section>

            <!-- 9. Troubleshooting & Recovery -->
            <section id="troubleshooting" class="scroll-mt-24 space-y-5">
                <div class="flex items-baseline gap-3">
                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">09</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Diagnostics & Frequently Asked Questions
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm sm:text-base">
                    Practical recovery guidance for common participant and administrator issues:
                </p>

                <!-- Clean Semantic Accordion / Details (Zero Clutter) -->
                <div class="space-y-3 pt-2">
                    <details class="group border border-slate-200 dark:border-teal-950/80 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30">
                        <summary class="font-semibold text-sm text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                            <span>Why does the system report "Duplicate vote detected"?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-3 pt-3 border-t border-slate-200 dark:border-teal-950/60">
                            SecurePoll detects that your browser or device has already submitted a ballot for this specific poll. Even in anonymous polls, device hash verification prevents casting multiple votes. If you share a computer with another participant, ensure the previous user closes their session and use a separate private browser profile.
                        </p>
                    </details>

                    <details class="group border border-slate-200 dark:border-teal-950/80 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30">
                        <summary class="font-semibold text-sm text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                            <span>Why is my private poll invitation link showing "Access Denied"?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-3 pt-3 border-t border-slate-200 dark:border-teal-950/60">
                            Private poll invitation tokens are strictly single-use and bound to the specific recipient email address. Access will be denied if: (1) the token has already been redeemed, (2) the token has expired, or (3) you are logged into an account with an email address different from the one on the invitation roster.
                        </p>
                    </details>

                    <details class="group border border-slate-200 dark:border-teal-950/80 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30">
                        <summary class="font-semibold text-sm text-slate-900 dark:text-white cursor-pointer list-none flex items-center justify-between">
                            <span>How long is an Email OTP verification code valid?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed mt-3 pt-3 border-t border-slate-200 dark:border-teal-950/60">
                            Email OTP verification codes are valid for exactly 10 minutes from dispatch and allow up to 3 entry attempts before automatic invalidation. A 60-second cooldown is enforced between resend requests to protect mail server reputation.
                        </p>
                    </details>
                </div>
            </section>

        </article>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
