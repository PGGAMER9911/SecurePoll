<?php
$pageTitle = 'Documentation — SecurePoll Voting Platform';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="flex flex-col lg:flex-row gap-12 items-start">

        <!-- Sticky Table of Contents Sidebar -->
        <aside class="w-full lg:w-64 shrink-0 lg:sticky lg:top-24 space-y-6">
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/70 shadow-xs">
                <div class="flex items-center gap-2 text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-3">
                    <i data-lucide="book" class="w-4 h-4"></i>
                    <span>Contents</span>
                </div>
                <nav class="space-y-1 text-xs font-medium">
                    <a href="#what-is-securepoll" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        1. What is SecurePoll
                    </a>
                    <a href="#getting-started" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        2. Getting Started
                    </a>
                    <a href="#creating-a-poll" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        3. Creating a Poll
                    </a>
                    <a href="#balloting-types" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        4. Balloting Types
                    </a>
                    <a href="#visibility-models" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        5. Visibility Models
                    </a>
                    <a href="#scheduling-quorums" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        6. Scheduling & Limits
                    </a>
                    <a href="#cryptographic-privacy" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        7. Cryptographic Privacy
                    </a>
                    <a href="#managing-sharing" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        8. Managing & Sharing
                    </a>
                    <a href="#accounts-settings" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        9. Accounts & Notifications
                    </a>
                    <a href="#troubleshooting" class="block py-1.5 px-2 rounded-lg text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100 dark:hover:bg-teal-950/40 transition-colors">
                        10. Troubleshooting & FAQs
                    </a>
                </nav>
            </div>

            <!-- Quick Action Box -->
            <div class="p-4 rounded-xl border border-teal-500/20 bg-teal-500/5 dark:bg-teal-950/30 text-xs space-y-2">
                <span class="font-semibold text-slate-900 dark:text-white">Need Live Verification?</span>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                    Test poll creation or participate in live ballots directly in the application.
                </p>
                <a href="<?php echo $basePrefix; ?>/polls" class="inline-flex items-center gap-1 font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                    <span>Open Dashboard</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </aside>

        <!-- Documentation Main Body -->
        <article class="flex-1 min-w-0 max-w-3xl space-y-16">

            <!-- Title & Intro -->
            <div class="border-b border-slate-200 dark:border-teal-950 pb-8">
                <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Product Manual</span>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mt-2 mb-3">
                    SecurePoll Documentation
                </h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                    Complete reference manual for configuring, managing, and voting on SecurePoll ballots. Covers privacy guarantees, voting modalities, access models, and administrative controls.
                </p>
            </div>

            <!-- 1. What is SecurePoll -->
            <section id="what-is-securepoll" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">1.</span> What is SecurePoll?
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    SecurePoll is an enterprise-grade digital voting and polling application engineered in PHP and MySQL. Designed for universities, student bodies, community organizations, and governance teams, SecurePoll eliminates the vulnerabilities of paper ballots and simplistic survey forms.
                </p>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 text-xs sm:text-sm space-y-2">
                    <h3 class="font-bold text-slate-900 dark:text-white">Core Architectural Tenets:</h3>
                    <ul class="list-disc pl-5 space-y-1 text-slate-600 dark:text-slate-300">
                        <li><strong>Cryptographic Anonymity:</strong> Cast votes cannot be traced back to user identities.</li>
                        <li><strong>Duplicate Vote Immunity:</strong> Enforces one-person-one-vote via device-anchored hash constraints, even across shared institutional Wi-Fi networks.</li>
                        <li><strong>Multi-Tenant Targeting:</strong> Structure eligibility by colleges, departments, and cohorts.</li>
                        <li><strong>Verifiable Audit Trails:</strong> Every administrative alteration is logged in tamper-evident audit tables.</li>
                    </ul>
                </div>
            </section>

            <!-- 2. Getting Started -->
            <section id="getting-started" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">2.</span> Getting Started
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    To participate in public polls, users can vote without an account if the poll creator has enabled anonymous guest voting. To create polls, manage voter rosters, or vote in restricted organizational elections, an account is required.
                </p>
                <div class="space-y-3">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">Step 1: Create an Account</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Navigate to <a href="<?php echo $basePrefix; ?>/register" class="text-teal-600 dark:text-teal-400 underline">Register</a>. Personal accounts are fully active today. Enter your name, email, and choose a secure password. A verification one-time passcode (OTP) will be dispatched to confirm your email.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">Step 2: Sign In</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Sign in with your email and password at <a href="<?php echo $basePrefix; ?>/login" class="text-teal-600 dark:text-teal-400 underline">Login</a>. If you log in from an unfamiliar browser, device trust verification will prompt an email OTP before establishing session credentials.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">Step 3: Access the Dashboard</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Access your active polls, private invitations, and analytics via the upper-right <strong>Go to Dashboard</strong> navigation action.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 3. Creating a Poll -->
            <section id="creating-a-poll" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">3.</span> Creating a Poll
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Authorized creators can launch new polls by selecting <strong>Create Poll</strong> from the dashboard header or visiting <code class="text-xs font-mono px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800">/polls/create</code>.
                </p>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 space-y-2 text-xs sm:text-sm">
                    <h3 class="font-bold text-slate-900 dark:text-white">Required Configuration Parameters:</h3>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600 dark:text-slate-300">
                        <li><strong>Poll Title:</strong> Clear, concise question or resolution for the ballot (required).</li>
                        <li><strong>Description:</strong> Context, background information, or voter instructions (optional).</li>
                        <li><strong>Voting Modality:</strong> Custom Options, Yes/No, or Rating Scale.</li>
                        <li><strong>Visibility:</strong> Public, Private, or Unlisted.</li>
                        <li><strong>Anonymity Toggle:</strong> Choose between anonymous cryptographic hashing and public voter audit trails.</li>
                    </ul>
                </div>
            </section>

            <!-- 4. Balloting Types -->
            <section id="balloting-types" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">4.</span> Supported Balloting Types
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    SecurePoll provides three distinct balloting modalities to accommodate various voting scenarios:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                            <i data-lucide="list-checks" class="w-4 h-4 text-teal-500"></i>
                            <span>Custom Options</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                            Provide two or more custom choices. Supports both <strong>Single Choice</strong> (one option allowed) and <strong>Multiple Choice</strong> (voters may select multiple options).
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                            <i data-lucide="thumbs-up" class="w-4 h-4 text-teal-500"></i>
                            <span>Yes / No / Abstain</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                            Binary resolution format. Automatically provides standardized Yes, No, and optional Abstain options for parliamentary and governance motions.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                            <i data-lucide="star" class="w-4 h-4 text-teal-500"></i>
                            <span>Rating Scale (1-5)</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
                            Numeric or star evaluation scale from 1 (lowest) to 5 (highest). Ideal for institutional feedback, candidate evaluation, and metric surveys.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 5. Visibility Models -->
            <section id="visibility-models" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">5.</span> Visibility & Access Models
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Ballot reach is controlled by three strictly segregated visibility tiers:
                </p>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white mb-1">
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs">Public</span>
                            <span>Visible on Public Feeds & Dashboard</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Listed in the public showcase and accessible to all verified members or community visitors according to creator settings.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white mb-1">
                            <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs">Private</span>
                            <span>Invitation & Scope Restricted</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Strictly invisible to outside users. Requires an explicit invitation token or membership in an authorized department/cohort. Attempting to open a private poll without authorization yields an access denied challenge.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white mb-1">
                            <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-xs">Unlisted</span>
                            <span>Direct Link Access Only</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Omitted from public showcase feeds and search results. Only users possessing the direct link (<code class="text-xs font-mono px-1 py-0.5 rounded bg-slate-100 dark:bg-slate-800">/polls/show?id=...</code>) may view and vote.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 6. Scheduling & Limits -->
            <section id="scheduling-quorums" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">6.</span> Scheduling, Quorums & Limits
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Ballots can be bound by exact timeframes and voter capacity caps:
                </p>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 text-xs sm:text-sm space-y-3">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-0.5">Start & End Schedules</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Setting a future start time places the poll in <span class="font-semibold text-amber-500">Upcoming</span> status. Voters cannot submit ballots before the opening moment. Reaching the end time automatically moves the poll to <span class="font-semibold text-slate-400">Closed</span>.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-0.5">Max Voters (Quorum Cap)</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Creators can set an explicit maximum number of voters (<code class="text-xs font-mono">max_voters</code>). Once the quota is fulfilled, the ballot automatically closes against further submissions.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-0.5">Result Visibility Controls</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Choose between <strong>Live</strong> (voters see instant aggregated tallies) and <strong>Hidden</strong> (tallies remain concealed until poll closes or are restricted to creator).
                        </p>
                    </div>
                </div>
            </section>

            <!-- 7. Cryptographic Privacy -->
            <section id="cryptographic-privacy" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">7.</span> Cryptographic Privacy & Anti-Tamper
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    SecurePoll implements a mathematically decoupled voting mechanism. When an anonymous poll is voted on:
                </p>
                <div class="p-5 rounded-xl border border-teal-500/20 bg-teal-500/5 dark:bg-teal-950/20 space-y-3 text-xs sm:text-sm">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        1. The voter's device is provisioned with a secure cryptographic device cookie (<code class="font-mono text-teal-600 dark:text-teal-400">sp_device_token</code>).
                    </p>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        2. The system computes a one-way salted SHA-256 hash:
                        <code class="block font-mono p-2.5 rounded bg-slate-900 text-teal-300 my-1 text-xs">
                            voter_hash = hash('sha256', poll_id + device_token + user_agent + salt)
                        </code>
                    </p>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        3. The vote is recorded with <code class="font-mono text-teal-600 dark:text-teal-400">user_id = NULL</code> and the unique <code class="font-mono text-teal-600 dark:text-teal-400">voter_hash</code>.
                    </p>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                        <strong>Outcome:</strong> Even database administrators inspecting raw database records cannot determine which user selected which option. Attempting to vote a second time triggers an atomic database UNIQUE collision, preventing duplicate voting.
                    </p>
                </div>
            </section>

            <!-- 8. Managing & Sharing -->
            <section id="managing-sharing" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">8.</span> Managing & Sharing Polls
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    From the <strong>Manage Poll</strong> dashboard (<code class="text-xs font-mono">/polls/manage?id=...</code>), poll owners have access to administrative tools:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <li><strong>Bulk Voter CSV Upload:</strong> Upload rosters of authorized voter emails for automatic token generation and invitation delivery.</li>
                    <li><strong>Single Voter Invitations:</strong> Search existing users and dispatch individual single-use invitations.</li>
                    <li><strong>Live Vote Analytics:</strong> Monitor turnout percentage, voter velocity, and device distribution in real time.</li>
                    <li><strong>Lifecycle Overrides:</strong> Pause, close, or archive polls ahead of schedule if emergency intervention is required.</li>
                </ul>
            </section>

            <!-- 9. Accounts & Notifications -->
            <section id="accounts-settings" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">9.</span> Accounts, Security & Notifications
                </h2>
                <div class="space-y-4 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    <p>
                        The <strong>Settings</strong> console allows users to update personal bios, manage security credentials, review trusted device sessions, and customize notification preferences.
                    </p>
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                            <i data-lucide="bell" class="w-4 h-4 text-teal-500"></i>
                            <span>In-App Notifications vs Email Notifications</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400">
                            <strong>In-App Notifications:</strong> Fully active. Alerts for poll invitations, poll start announcements, poll ending reminders, and published results are delivered in real time to the top navigation bell dropdown.
                        </p>
                        <p class="text-slate-600 dark:text-slate-400">
                            <strong>Email Notifications (Coming Soon):</strong> Marked as Coming Soon in Settings. Mandatory security emails (password resets, OTP verification) are delivered via SMTP, while optional digest email delivery is in scheduled development.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 10. Troubleshooting & FAQs -->
            <section id="troubleshooting" class="scroll-mt-28 space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="text-teal-500">10.</span> Troubleshooting & Common Questions
                </h2>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Q: Why did I receive "Duplicate vote detected"?</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            SecurePoll detected that your browser/device has already submitted a ballot for this poll. Even in anonymous polls, our device token hash guarantees that each voter participates only once.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Q: Why does my private poll invitation link say "Access Denied"?</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Private poll invitation tokens are single-use and bound to authorized email addresses. If the token has expired, already been accepted, or if you are signed in with a different email than the invited roster, access is rejected.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-teal-950 bg-white dark:bg-[#07181b]/50">
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Q: Can administrators see who I voted for?</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            No. For all polls configured with the default Anonymous mode, vote records have no foreign key or column linking back to the user account ID. Only the aggregate tallies are stored.
                        </p>
                    </div>
                </div>
            </section>

        </article>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';
?>
