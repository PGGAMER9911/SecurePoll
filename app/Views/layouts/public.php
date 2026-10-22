<?php
$userTheme = \app\Helpers\Session::get('theme') ?? ($_COOKIE['securepoll_theme'] ?? 'system');
if (!in_array($userTheme, ['dark', 'light', 'system'], true)) {
    $userTheme = 'system';
}

$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = str_replace('/SecurePoll/public', '', $currentPath);
if ($currentPath === '') $currentPath = '/';

$isLoggedIn = \app\Helpers\Session::get('user_id') !== null;
$dashboardUrl = $isLoggedIn ? ($basePrefix . '/dashboard') : ($basePrefix . '/polls');
$pageDesc = $pageDescription ?? 'SecurePoll is a place to ask everyday questions, compare opinions, and run polls with friends, gamers, and small communities.';
$canonicalUrl = 'https://securepoll.pgdev.in' . ($currentPath === '/' ? '/' : $currentPath);
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $userTheme === 'dark' ? 'dark' : ''; ?> h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SecurePoll — Small questions. Different perspectives.'; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:site_name" content="SecurePoll">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle ?? 'SecurePoll — Small questions. Different perspectives.'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="https://securepoll.pgdev.in/images/logo-square.png">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle ?? 'SecurePoll — Small questions. Different perspectives.'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDesc); ?>">
    <meta name="twitter:image" content="https://securepoll.pgdev.in/images/logo-square.png">

    <meta name="csrf-token" content="<?php echo \app\Helpers\Security::generateCsrfToken(); ?>">
    <link rel="icon" type="image/png" href="<?php echo $basePrefix; ?>/images/logo-square.png">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?php echo $basePrefix; ?>/css/app.css?v=<?php echo file_exists(__DIR__ . '/../../../public/css/app.css') ? filemtime(__DIR__ . '/../../../public/css/app.css') : '1.0'; ?>" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Unified Theme Engine -->
    <script src="<?php echo $basePrefix; ?>/js/theme.js"></script>

    <script>
        window.SecurePollBase = '<?php echo $basePrefix; ?>';
    </script>

    <!-- Immediate Anti-FOUC Theme Script -->
    <script>
        (function() {
            let activeTheme = '<?php echo $userTheme; ?>';
            try {
                const stored = localStorage.getItem('securepoll_theme');
                if (stored && ['dark', 'light', 'system'].includes(stored)) {
                    activeTheme = stored;
                }
            } catch(e) {}

            if (activeTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (activeTheme === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        })();
    </script>
</head>
<body class="min-h-full bg-slate-50 dark:bg-[#071416] text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col selection:bg-teal-500/20 selection:text-teal-900 dark:selection:text-teal-200 transition-colors duration-200">

    <!-- Compact Understated Navbar -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-200/80 dark:border-teal-950/80 bg-white/85 dark:bg-[#071416]/85 backdrop-blur-md transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Brand Logo -->
            <a href="<?php echo $basePrefix ?: '/'; ?>" class="flex items-center gap-2.5 group shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 rounded-lg">
                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 dark:border-teal-800/60 shadow-xs flex items-center justify-center bg-white dark:bg-slate-900">
                    <img src="<?php echo $basePrefix; ?>/images/logo-square.png" alt="SecurePoll Logo" class="w-7 h-7 object-contain">
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-bold text-lg tracking-tight text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">SecurePoll</span>
                    <span class="hidden sm:inline-block text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">Voting</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="<?php echo $basePrefix ?: '/'; ?>" class="px-3 py-1.5 rounded-lg transition-colors <?php echo $currentPath === '/' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">
                    Home
                </a>
                <a href="<?php echo $basePrefix; ?>/about" class="px-3 py-1.5 rounded-lg transition-colors <?php echo $currentPath === '/about' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">
                    About
                </a>
                <a href="<?php echo $basePrefix; ?>/docs" class="px-3 py-1.5 rounded-lg transition-colors <?php echo in_array($currentPath, ['/docs', '/documentation'], true) ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">
                    Documentation
                </a>
                <a href="<?php echo $basePrefix; ?>/pricing" class="px-3 py-1.5 rounded-lg transition-colors <?php echo $currentPath === '/pricing' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">
                    Pricing
                </a>
                <a href="<?php echo $basePrefix; ?>/contact" class="px-3 py-1.5 rounded-lg transition-colors <?php echo $currentPath === '/contact' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">
                    Contact
                </a>
            </nav>

            <!-- Right Controls: Theme Switcher + Dashboard CTA + Mobile Toggle -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Theme Switcher -->
                <div class="relative flex items-center p-0.5 bg-slate-100 dark:bg-slate-900/90 rounded-lg border border-slate-200 dark:border-teal-950 w-[84px] sm:w-[88px] h-7 shrink-0">
                    <div id="theme-active-pill" class="absolute top-0.5 bottom-0.5 w-[calc(33.33%-1.33px)] bg-white dark:bg-teal-900/50 rounded shadow-xs border border-slate-200/80 dark:border-teal-700/50 transition-transform duration-200 ease-out z-0 pointer-events-none"></div>
                    <button type="button" class="theme-btn relative flex-1 flex justify-center items-center h-full text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors z-10" data-theme="light" title="Light Theme" aria-label="Light Theme">
                        <i data-lucide="sun" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" class="theme-btn relative flex-1 flex justify-center items-center h-full text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors z-10" data-theme="system" title="System Theme" aria-label="System Theme">
                        <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" class="theme-btn relative flex-1 flex justify-center items-center h-full text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors z-10" data-theme="dark" title="Dark Theme" aria-label="Dark Theme">
                        <i data-lucide="moon" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <?php if ($isLoggedIn): ?>
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="<?php echo $basePrefix; ?>/dashboard" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 transition-all shadow-xs hover:shadow-teal-500/20 active:translate-y-px">
                            <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                            <span>Go to Dashboard</span>
                        </a>
                        <a href="<?php echo $basePrefix; ?>/logout" class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 px-2 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors" title="Sign Out">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Sign Out</span>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $basePrefix; ?>/login" class="hidden sm:inline-flex text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 px-2 py-1.5 transition-colors">
                        Sign In
                    </a>
                    <a href="<?php echo $basePrefix; ?>/register" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 transition-all shadow-xs hover:shadow-teal-500/20 active:translate-y-px">
                        <span>Register</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button (min-h-[44px] min-w-[44px] touch target) -->
                <button type="button" id="mobileMenuBtn" class="md:hidden flex items-center justify-center w-11 h-11 text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500" aria-label="Toggle Navigation Menu">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Navigation -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 dark:border-teal-950/80 bg-white/95 dark:bg-[#071416]/95 backdrop-blur-md px-4 py-4 space-y-2">
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo $basePrefix; ?>/dashboard" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-xs mb-3 min-h-[44px]">
                    <span class="flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Go to Dashboard</span>
                    </span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo $basePrefix; ?>/polls" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-xs mb-3 min-h-[44px]">
                    <span class="flex items-center gap-2">
                        <i data-lucide="vote" class="w-4 h-4"></i>
                        <span>Explore Active Polls</span>
                    </span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            <?php endif; ?>

            <a href="<?php echo $basePrefix ?: '/'; ?>" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium min-h-[44px] <?php echo $currentPath === '/' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">Home</a>
            <a href="<?php echo $basePrefix; ?>/about" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium min-h-[44px] <?php echo $currentPath === '/about' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">About</a>
            <a href="<?php echo $basePrefix; ?>/docs" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium min-h-[44px] <?php echo in_array($currentPath, ['/docs', '/documentation'], true) ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">Documentation</a>
            <a href="<?php echo $basePrefix; ?>/pricing" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium min-h-[44px] <?php echo $currentPath === '/pricing' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">Pricing</a>
            <a href="<?php echo $basePrefix; ?>/contact" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium min-h-[44px] <?php echo $currentPath === '/contact' ? 'text-teal-600 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/50'; ?>">Contact</a>

            <?php if ($isLoggedIn): ?>
                <div class="pt-3 border-t border-slate-200 dark:border-teal-950/80 flex items-center justify-between gap-3">
                    <a href="<?php echo $basePrefix; ?>/dashboard" class="flex-1 text-center py-2.5 rounded-xl border border-teal-600/40 text-sm font-medium text-teal-600 dark:text-teal-400 min-h-[44px] flex items-center justify-center">Dashboard</a>
                    <a href="<?php echo $basePrefix; ?>/logout" class="flex-1 text-center py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-sm font-medium text-rose-600 dark:text-rose-400 min-h-[44px] flex items-center justify-center">Sign Out</a>
                </div>
            <?php else: ?>
                <div class="pt-3 border-t border-slate-200 dark:border-teal-950/80 flex items-center justify-between gap-3">
                    <a href="<?php echo $basePrefix; ?>/login" class="flex-1 text-center py-2.5 rounded-xl border border-slate-200 dark:border-teal-900/60 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-teal-500 min-h-[44px] flex items-center justify-center">Sign In</a>
                    <a href="<?php echo $basePrefix; ?>/register" class="flex-1 text-center py-2.5 rounded-xl bg-teal-600 text-sm font-medium text-white hover:bg-teal-500 min-h-[44px] flex items-center justify-center">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Page Content -->
    <main class="flex-1 w-full min-w-0">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Clean, Simple Footer -->
    <footer class="border-t border-slate-200/80 dark:border-teal-950/80 bg-white/50 dark:bg-[#050f11] py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                
                <!-- Col 1: Brand & Identity -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <img src="<?php echo $basePrefix; ?>/images/logo-square.png" alt="SecurePoll" class="w-7 h-7 object-contain rounded">
                        <span class="font-bold text-base tracking-tight text-slate-900 dark:text-white">SecurePoll</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm leading-relaxed">
                        An early-stage polling platform for everyday questions, friendly debates, and community feedback. Built with privacy in mind and improving step by step.
                    </p>
                    <div class="pt-1">
                        <a href="mailto:contact@pgdev.in" class="inline-flex items-center gap-1.5 text-xs text-teal-600 dark:text-teal-400 hover:underline">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                            <span>contact@pgdev.in</span>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div class="space-y-2.5">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider">Navigation</h4>
                    <ul class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                        <li><a href="<?php echo $basePrefix ?: '/'; ?>" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Home</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/about" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">About & Creators</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/docs" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Documentation</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/pricing" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Pricing & Availability</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/contact" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Col 3: Platform Application -->
                <div class="space-y-2.5">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-slate-200 uppercase tracking-wider">Application</h4>
                    <ul class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                        <li><a href="<?php echo $basePrefix; ?>/polls" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Browse Public Polls</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/polls/create" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Create a Poll</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/dashboard" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Voter Dashboard</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/login" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Sign In</a></li>
                        <li><a href="<?php echo $basePrefix; ?>/register" class="hover:text-teal-600 dark:hover:text-teal-300 transition-colors">Register Account</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Line -->
            <div class="pt-8 border-t border-slate-200 dark:border-teal-950/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400 dark:text-slate-500">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span>&copy; <?php echo date('Y'); ?> SecurePoll. Source-Available Licence.</span>
                    <span class="inline-block w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                    <span>Designed & Engineered by Parth Gamit and Contributors</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="https://github.com/PGGAMER9911/SecurePoll" target="_blank" rel="noopener noreferrer" class="hover:text-slate-700 dark:hover:text-slate-300 transition-colors flex items-center gap-1.5">
                        <i data-lucide="code-2" class="w-4 h-4"></i>
                        <span>GitHub Repository</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide Icons
        try {
            lucide.createIcons();
        } catch(e) {}

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Auto initialize unified theme engine
        if (window.SecurePollTheme) {
            window.SecurePollTheme.init('<?php echo $userTheme; ?>');
        }
    </script>
</body>
</html>
