<?php
$userTheme = \app\Helpers\Session::get('theme') ?? ($_COOKIE['securepoll_theme'] ?? 'system');
$basePrefix = \app\Helpers\Url::base();
if (!in_array($userTheme, ['dark', 'light', 'system'], true)) {
    $userTheme = 'system';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $userTheme === 'dark' ? 'dark' : ''; ?> h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SecurePoll'; ?></title>
    <meta name="csrf-token" content="<?php echo \app\Helpers\Security::generateCsrfToken(); ?>">
    <link rel="icon" type="image/png" href="<?php echo $basePrefix; ?>/images/logo-square.png">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?php echo $basePrefix; ?>/css/app.css?v=<?php echo file_exists(__DIR__ . '/../../../public/css/app.css') ? filemtime(__DIR__ . '/../../../public/css/app.css') : '1.0'; ?>" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Unified Theme Engine -->
    <script src="<?php echo $basePrefix; ?>/js/theme.js"></script>

    <!-- Unified Loader & Navigation Progress -->
    <script src="<?php echo $basePrefix; ?>/js/loader.js"></script>

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
    <meta name="robots" content="noindex, nofollow">
    <script>
        window.SecurePollBase = '<?php echo $basePrefix; ?>';
    </script>
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col selection:bg-teal-500/20 selection:text-teal-900 dark:selection:text-teal-200 transition-colors duration-200">
    <!-- Top Navigation Progress Bar -->
    <div id="securepoll-top-progress" class="fixed top-0 left-0 h-[2px] w-0 bg-teal-600 dark:bg-teal-400 z-[10000] pointer-events-none opacity-0" aria-hidden="true"></div>

    <!-- Initial Branded Page Loading Screen -->
    <div id="securepoll-page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white dark:bg-slate-950 pointer-events-auto" role="status" aria-live="polite" aria-label="Loading SecurePoll">
        <div class="flex flex-col items-center justify-center gap-4 select-none">
            <!-- Branded Icon with Subtle Spinner Ring -->
            <div class="relative flex items-center justify-center w-14 h-14">
                <div class="absolute inset-0 rounded-2xl border-2 border-teal-600/20 dark:border-teal-400/20"></div>
                <div class="absolute inset-0 rounded-2xl border-2 border-teal-600 dark:border-teal-400 border-t-transparent animate-spin"></div>
                <img src="<?php echo $basePrefix; ?>/images/logo-square.png" alt="SecurePoll" class="w-9 h-9 rounded-xl object-contain shadow-xs">
            </div>
            <!-- Accessible Loading Text -->
            <div class="flex flex-col items-center gap-1 text-center">
                <span class="text-xs font-semibold tracking-wide text-slate-800 dark:text-slate-200">Loading SecurePoll</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Preparing your workspace…</span>
            </div>
        </div>
    </div>
    <noscript>
        <style>#securepoll-page-loader { display: none !important; }</style>
    </noscript>

    <!-- Guest Header -->
    <header class="h-16 shrink-0 flex items-center justify-between px-4 sm:px-8 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md">
        <a href="<?php echo $basePrefix ?: '/'; ?>" class="flex items-center gap-3 group">
            <img src="<?php echo $basePrefix; ?>/images/logo-square.png" alt="SecurePoll" class="w-10 h-10 rounded-xl shrink-0 object-contain shadow-xs">
            <span class="font-bold text-xl tracking-tight text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">SecurePoll</span>
        </a>

        <div class="flex items-center gap-3 sm:gap-4">
            <a href="<?php echo $basePrefix ?: '/'; ?>" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 transition-colors px-2.5 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800/60">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Home</span>
            </a>

            <!-- Theme Switcher -->
            <div class="relative flex items-center p-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700/80 w-[96px] h-8 shrink-0">
                <div id="theme-active-pill" class="absolute top-0.5 bottom-0.5 w-[calc(33.33%-1.33px)] bg-white dark:bg-slate-700 rounded-md shadow-xs border border-slate-200/80 dark:border-slate-600 transition-transform duration-200 ease-out z-0 pointer-events-none"></div>
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
        </div>
    </header>

    <!-- Flash Messages (Top Right Toast for Guest) -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        <?php if ($msg = \app\Helpers\Session::getFlash('error')): ?>
            <div class="pointer-events-auto bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-900/60 text-slate-900 dark:text-white px-4 py-3 rounded-xl shadow-lg flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 text-sm font-medium pt-0.5"><?php echo htmlspecialchars($msg); ?></div>
            </div>
        <?php endif; ?>
        <?php if ($msg = \app\Helpers\Session::getFlash('success')): ?>
            <div class="pointer-events-auto bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/60 text-slate-900 dark:text-white px-4 py-3 rounded-xl shadow-lg flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 text-sm font-medium pt-0.5"><?php echo htmlspecialchars($msg); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Container -->
    <main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md mx-auto my-auto">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Guest Footer -->
    <footer class="py-4 px-4 text-center border-t border-slate-200/80 dark:border-slate-800/80 text-xs text-slate-400 dark:text-slate-500">
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
            <span>&copy; <?php echo date('Y'); ?> SecurePoll</span>
            <span class="inline-block w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
            <span>Cryptographically Audited Polling</span>
        </div>
    </footer>

    <script>
        // Initialize Lucide Icons
        try {
            lucide.createIcons();
        } catch(e) {}

        document.addEventListener("DOMContentLoaded", () => {
            // Auto-dismiss toasts after 5 seconds
            setTimeout(() => {
                const toasts = document.querySelectorAll('#toast-container > div');
                toasts.forEach(t => {
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(-8px)';
                    t.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    setTimeout(() => t.remove(), 250);
                });
            }, 5000);

            // Initialize Unified Theme Engine
            if (window.SecurePollTheme) {
                window.SecurePollTheme.init('<?php echo $userTheme; ?>');
            }
        });
    </script>
</body>
</html>
