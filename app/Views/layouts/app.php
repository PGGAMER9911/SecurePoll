<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$userTheme = 'system';
if (\app\Helpers\Session::has('user_id')) {
    $userTheme = \app\Helpers\Session::get('theme');
    if (!$userTheme) {
        $userModel = new \app\Models\User();
        $u = $userModel->findById(\app\Helpers\Session::get('user_id'));
        $userTheme = $u['theme'] ?? 'system';
        \app\Helpers\Session::set('theme', $userTheme);
    }
} else {
    $userTheme = \app\Helpers\Session::get('theme') ?? ($_COOKIE['securepoll_theme'] ?? 'system');
}
if (!in_array($userTheme, ['dark', 'light', 'system'], true)) {
    $userTheme = 'system';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $userTheme === 'dark' ? 'dark' : ''; ?> h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard - SecurePoll'; ?></title>
    <meta name="csrf-token" content="<?php echo \app\Helpers\Security::generateCsrfToken(); ?>">
    
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="<?php echo $basePrefix; ?>/images/logo-square.png">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <link href="<?php echo $basePrefix; ?>/css/app.css?v=<?php echo file_exists(__DIR__ . '/../../../public/css/app.css') ? filemtime(__DIR__ . '/../../../public/css/app.css') : '1.0'; ?>" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>window.SecurePollBase = '<?php echo $basePrefix; ?>';</script>

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
                    <?php if (!\app\Helpers\Session::get('user_id')): ?>
                    activeTheme = stored;
                    <?php endif; ?>
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

<body class="min-h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col lg:flex-row selection:bg-teal-500/20 selection:text-teal-900 dark:selection:text-teal-200 transition-colors duration-200">

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

    <!-- Flash Messages (Top Right Toast) -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
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

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 dark:bg-slate-950/80 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed z-50 inset-y-0 left-0 w-64 transform -translate-x-full lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 transition-transform lg:transition-all duration-200 ease-in-out flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 shrink-0">
        <!-- Logo Header -->
        <div class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-800 justify-between shrink-0 sidebar-header">
            <a href="<?php echo $basePrefix ?: '/'; ?>" class="flex items-center gap-3 overflow-hidden sidebar-logo-container group min-w-0" title="Return to Homepage">
                <img src="<?php echo $basePrefix; ?>/images/logo-square.png" alt="SecurePoll" class="w-10 h-10 rounded-xl shrink-0 object-contain shadow-xs">
                <span class="sidebar-text font-bold text-lg tracking-tight text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors truncate">SecurePoll</span>
            </a>
            <!-- Desktop Collapse Toggle (Header) -->
            <button type="button" aria-label="Toggle sidebar width" class="desktop-collapse-toggle hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                <i data-lucide="chevron-left" class="w-4 h-4 collapse-icon transition-transform"></i>
            </button>
            <!-- Mobile Close Button -->
            <button id="closeSidebarBtn" type="button" aria-label="Close navigation" class="lg:hidden text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar min-h-0">
            <?php 
                $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
                
                $navItems = [
                    ['url' => $basePrefix ?: '/', 'icon' => 'home', 'label' => 'Home'],
                    ['url' => $basePrefix . '/dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                    ['url' => $basePrefix . '/polls', 'icon' => 'vote', 'label' => 'Explore Polls'],
                    ['url' => $basePrefix . '/polls/create', 'icon' => 'plus-circle', 'label' => 'Create Poll'],
                    ['url' => $basePrefix . '/polls/my-polls', 'icon' => 'folder-kanban', 'label' => 'My Polls'],
                    ['url' => $basePrefix . '/invitations', 'icon' => 'mail', 'label' => 'Invitations'],
                    ['url' => $basePrefix . '/dashboard/analytics', 'icon' => 'bar-chart-3', 'label' => 'Analytics'],
                ];
                
                $activeUrl = '';
                $maxLength = 0;
                foreach ($navItems as $item) {
                    if (strpos($currentPath, $item['url']) === 0) {
                        if (strlen($item['url']) > $maxLength) {
                            $maxLength = strlen($item['url']);
                            $activeUrl = $item['url'];
                        }
                    }
                }
                
                foreach ($navItems as $item) {
                    $isActive = ($item['url'] === $activeUrl);
                    $activeClass = $isActive 
                        ? 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 font-semibold' 
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium';
                    $iconActive = $isActive 
                        ? 'text-teal-600 dark:text-teal-400' 
                        : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-200';

                    echo sprintf('
                        <a href="%s" title="%s" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors %s">
                            <i data-lucide="%s" class="w-4 h-4 shrink-0 transition-colors %s"></i>
                            <span class="sidebar-text truncate">%s</span>
                        </a>
                    ', htmlspecialchars($item['url']), htmlspecialchars($item['label']), $activeClass, htmlspecialchars($item['icon']), $iconActive, htmlspecialchars($item['label']));
                }
            ?>

            <div class="pt-5 pb-1 sidebar-text-container">
                <p class="px-3 text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider sidebar-text">Preferences</p>
            </div>
            
            <a href="<?php echo $basePrefix; ?>/notifications" title="Notifications" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo strpos($currentPath, $basePrefix . '/notifications') === 0 ? 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium'; ?>">
                <i data-lucide="bell" class="w-4 h-4 shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-200"></i>
                <span class="sidebar-text truncate">Notifications</span>
            </a>
            <a href="<?php echo $basePrefix; ?>/settings" title="Settings" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors <?php echo $currentPath === ($basePrefix . '/settings') ? 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white font-medium'; ?>">
                <i data-lucide="settings" class="w-4 h-4 shrink-0 text-slate-400 dark:text-slate-500 group-hover:text-slate-700 dark:group-hover:text-slate-200"></i>
                <span class="sidebar-text truncate">Settings</span>
            </a>
        </div>

        <!-- User Profile Footer & Account Navigation -->
        <div class="sidebar-footer p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/60 shrink-0 space-y-1">
            <?php if (\app\Helpers\Session::get('user_id')): ?>
                <!-- User Profile Link -->
                <a href="<?php echo $basePrefix; ?>/profile?id=<?php echo \app\Helpers\Session::get('user_id'); ?>" title="View Profile" class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-slate-600 dark:text-slate-400 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center font-bold text-[11px] shrink-0">
                        <?php echo strtoupper(substr(\app\Helpers\Session::get('user_name') ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="overflow-hidden sidebar-text min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"><?php echo htmlspecialchars(\app\Helpers\Session::get('user_name') ?? 'User'); ?></p>
                        <!--email_off--><p class="text-[11px] text-slate-500 dark:text-slate-400 truncate"><?php echo htmlspecialchars(\app\Helpers\Session::get('user_email') ?? ''); ?></p><!--/email_off-->
                    </div>
                </a>

                <!-- Integrated Logout Action -->
                <form id="logoutForm" action="<?php echo $basePrefix; ?>/logout" method="POST" class="m-0 w-full">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\app\Helpers\Security::generateCsrfToken()); ?>">
                    <button type="submit" id="logoutBtn" title="Sign Out" aria-label="Sign Out" class="group flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm font-medium transition-colors text-slate-600 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 hover:text-rose-600 dark:hover:text-rose-400 cursor-pointer">
                        <i data-lucide="log-out" class="w-4 h-4 shrink-0 transition-colors text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400"></i>
                        <span class="sidebar-text truncate">Sign Out</span>
                    </button>
                </form>

                <!-- Desktop Collapse / Expand Toggle (Bottom) -->
                <button type="button" aria-label="Toggle sidebar width" title="Toggle sidebar" class="desktop-collapse-toggle hidden lg:flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm font-medium transition-colors text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/60 cursor-pointer">
                    <i data-lucide="chevron-left" class="w-4 h-4 collapse-icon transition-transform shrink-0"></i>
                    <span class="sidebar-text truncate">Collapse sidebar</span>
                </button>
            <?php else: ?>
                <div class="space-y-1.5">
                    <a href="<?php echo $basePrefix; ?>/login" class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-xs font-medium">
                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i> <span class="sidebar-text">Sign In</span>
                    </a>
                    <a href="<?php echo $basePrefix; ?>/register" class="flex items-center justify-center gap-2 w-full py-2 px-3 rounded-lg bg-teal-600 hover:bg-teal-500 text-white transition-colors text-xs font-medium shadow-xs">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> <span class="sidebar-text">Register</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 min-h-screen relative">
        
        <!-- Top Navbar -->
        <header class="h-16 shrink-0 flex items-center justify-between px-4 sm:px-6 lg:px-8 border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <!-- Mobile Sidebar Toggle -->
                <button id="openSidebarBtn" type="button" aria-label="Open navigation menu" class="lg:hidden text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white tracking-tight truncate"><?php echo $pageTitle ?? 'SecurePoll'; ?></h1>
            </div>
            
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Theme Switcher Pill -->
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

                <!-- Notifications Dropdown -->
                <div class="relative" id="notificationsDropdownWrapper">
                    <button id="notificationsToggleBtn" type="button" aria-label="Notifications" class="relative text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <span id="unreadBadge" class="hidden absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                    </button>
                    
                    <!-- Dropdown Panel -->
                    <div id="notificationsDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 overflow-hidden z-50 origin-top-right transition-all">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/60">
                            <h3 class="font-semibold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Notifications</h3>
                            <button id="markAllReadBtn" type="button" class="text-xs text-teal-600 dark:text-teal-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Mark all read</button>
                        </div>
                        <div id="notificationsList" class="max-h-[60vh] overflow-y-auto custom-scrollbar divide-y divide-slate-100 dark:divide-slate-800/60">
                            <div class="p-6 text-center text-xs text-slate-400">Loading notifications...</div>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/60">
                            <a href="<?php echo $basePrefix; ?>/notifications" class="block w-full text-center px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">View all notifications &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body (Natural Document Scrolling) -->
        <div class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8" id="scrollable-content">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <script>
        // Initialize Lucide Icons
        try {
            lucide.createIcons();
        } catch(e) { console.error('Lucide error', e); }
        
        document.addEventListener("DOMContentLoaded", () => {
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const openSidebarBtn = document.getElementById('openSidebarBtn');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            const collapseToggles = document.querySelectorAll('.desktop-collapse-toggle');
            let isSidebarCollapsed = localStorage.getItem('securepoll_sidebar_collapsed') === 'true';

            function applySidebarState(collapsed) {
                if (!sidebar) return;
                const header = sidebar.querySelector('.sidebar-header');
                const logoContainer = sidebar.querySelector('.sidebar-logo-container');
                const headerCollapseBtn = header ? header.querySelector('.desktop-collapse-toggle') : null;
                const itemsToCenter = sidebar.querySelectorAll('.flex-1 a, .sidebar-footer a, .sidebar-footer button');

                if (collapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    if (header) {
                        header.classList.remove('px-4', 'justify-between');
                        header.classList.add('px-2', 'justify-center');
                    }
                    if (logoContainer) {
                        logoContainer.classList.add('justify-center');
                    }
                    if (headerCollapseBtn) {
                        headerCollapseBtn.style.display = 'none';
                    }
                    itemsToCenter.forEach(el => el.classList.add('justify-center'));
                    collapseToggles.forEach(toggle => {
                        const icon = toggle.querySelector('.collapse-icon');
                        if (icon) {
                            icon.classList.remove('rotate-0');
                            icon.classList.add('rotate-180');
                        }
                    });
                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.sidebar-text-container').forEach(el => el.classList.add('hidden'));
                } else {
                    sidebar.classList.add('w-64');
                    sidebar.classList.remove('w-20');
                    if (header) {
                        header.classList.add('px-4', 'justify-between');
                        header.classList.remove('px-2', 'justify-center');
                    }
                    if (logoContainer) {
                        logoContainer.classList.remove('justify-center');
                    }
                    if (headerCollapseBtn) {
                        headerCollapseBtn.style.display = '';
                    }
                    itemsToCenter.forEach(el => el.classList.remove('justify-center'));
                    collapseToggles.forEach(toggle => {
                        const icon = toggle.querySelector('.collapse-icon');
                        if (icon) {
                            icon.classList.remove('rotate-180');
                            icon.classList.add('rotate-0');
                        }
                    });
                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.sidebar-text-container').forEach(el => el.classList.remove('hidden'));
                }
            }

            if (window.innerWidth >= 1024) {
                applySidebarState(isSidebarCollapsed);
            }

            collapseToggles.forEach(btn => {
                btn.addEventListener('click', () => {
                    isSidebarCollapsed = !isSidebarCollapsed;
                    localStorage.setItem('securepoll_sidebar_collapsed', isSidebarCollapsed);
                    applySidebarState(isSidebarCollapsed);
                });
            });

            function toggleSidebar() {
                if (!sidebar) return;
                const isOpen = sidebar.classList.contains('translate-x-0');
                if (isOpen) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop?.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    sidebarBackdrop?.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            }

            if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
            if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
            if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', toggleSidebar);
            
            // Initialize Unified Theme Engine
            if (window.SecurePollTheme) {
                window.SecurePollTheme.init('<?php echo $userTheme; ?>');
            }

            // Logout Button Loading & Double-submit Prevention
            const logoutForm = document.getElementById('logoutForm');
            if (logoutForm) {
                logoutForm.addEventListener('submit', () => {
                    const logoutBtn = document.getElementById('logoutBtn');
                    if (logoutBtn && !logoutBtn.disabled) {
                        logoutBtn.disabled = true;
                        logoutBtn.setAttribute('aria-busy', 'true');
                        const icon = logoutBtn.querySelector('i');
                        if (icon) {
                            icon.outerHTML = '<i data-lucide="loader-2" class="w-4 h-4 shrink-0 animate-spin text-rose-500"></i>';
                            if (window.lucide) window.lucide.createIcons();
                        }
                    }
                });
            }

            // Toast Auto-dismiss
            setTimeout(() => {
                const toasts = document.querySelectorAll('#toast-container > div');
                toasts.forEach(t => {
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(-8px)';
                    t.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    setTimeout(() => t.remove(), 250);
                });
            }, 5000);
        });
    </script>
    <?php if (\app\Helpers\Session::get('user_id')): ?>
    <script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            if (!sessionStorage.getItem('bfcache_reloaded_' + window.location.pathname)) {
                sessionStorage.setItem('bfcache_reloaded_' + window.location.pathname, 'true');
                window.location.reload();
            }
        } else {
            sessionStorage.removeItem('bfcache_reloaded_' + window.location.pathname);
        }
    });
    </script>
    <?php endif; ?>
    <script>
        // Notifications JS
        document.addEventListener("DOMContentLoaded", () => {
            const notifToggle = document.getElementById('notificationsToggleBtn');
            const notifDropdown = document.getElementById('notificationsDropdown');
            const notifList = document.getElementById('notificationsList');
            const unreadBadge = document.getElementById('unreadBadge');
            const markAllBtn = document.getElementById('markAllReadBtn');
            let notifsLoaded = false;
            
            if (notifToggle && notifDropdown) {
                notifToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = notifDropdown.classList.contains('hidden');
                    if (isHidden) {
                        notifDropdown.classList.remove('hidden');
                        if (!notifsLoaded) fetchLatestNotifications();
                    } else {
                        notifDropdown.classList.add('hidden');
                    }
                });
                
                document.addEventListener('click', (e) => {
                    if (!notifDropdown.classList.contains('hidden') && !notifToggle.contains(e.target) && !notifDropdown.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
                
                notifDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
                
                if (markAllBtn) {
                    markAllBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        fetch((window.SecurePollBase || '') + '/notifications/markAllRead', {
                            method: 'POST',
                            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'csrf_token=' + (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
                        }).then(r => r.json()).then(res => {
                            if (res.success) {
                                unreadBadge?.classList.add('hidden');
                                fetchLatestNotifications();
                            }
                        });
                    });
                }
                
                function fetchUnreadCount() {
                    fetch((window.SecurePollBase || '') + '/notifications/unreadCount')
                        .then(r => r.json())
                        .then(data => {
                            if (data.success && data.count > 0) {
                                unreadBadge?.classList.remove('hidden');
                            } else {
                                unreadBadge?.classList.add('hidden');
                            }
                        }).catch(e => {});
                }
                
                function fetchLatestNotifications() {
                    if (!notifList) return;
                    notifList.innerHTML = '<div class="p-6 text-center text-xs text-slate-400">Loading...</div>';
                    fetch((window.SecurePollBase || '') + '/notifications/latest')
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                notifsLoaded = true;
                                if (data.unread_count > 0) unreadBadge?.classList.remove('hidden');
                                else unreadBadge?.classList.add('hidden');
                                
                                if (data.data.length === 0) {
                                    notifList.innerHTML = '<div class="p-8 text-center"><i data-lucide="bell-off" class="w-6 h-6 mx-auto text-slate-300 dark:text-slate-600 mb-2"></i><p class="text-xs text-slate-500 dark:text-slate-400">No notifications yet</p></div>';
                                    if (window.lucide) lucide.createIcons();
                                    return;
                                }
                                
                                let html = '';
                                data.data.forEach(n => {
                                    const icon = n.icon || 'bell';
                                    const isUnread = n.is_read == 0;
                                    const bgClass = isUnread ? 'bg-indigo-50/50 dark:bg-indigo-950/20' : '';
                                    const dot = isUnread ? '<span class="w-2 h-2 rounded-full bg-teal-600 dark:bg-teal-400 shrink-0 mt-1.5"></span>' : '';
                                    
                                    let url = '#';
                                    if (n.entity_type === 'poll' && n.entity_id) {
                                        if (n.action_type === 'view_poll') url = (window.SecurePollBase || '') + '/polls/show?id=' + n.entity_id;
                                        if (n.action_type === 'view_invitations') url = (window.SecurePollBase || '') + '/invitations';
                                    }
                                    
                                    const d = new Date(n.created_at);
                                    const now = new Date();
                                    const diff = Math.floor((now - d) / 1000);
                                    let timeStr = 'just now';
                                    if (diff > 60) timeStr = Math.floor(diff/60) + 'm ago';
                                    if (diff > 3600) timeStr = Math.floor(diff/3600) + 'h ago';
                                    if (diff > 86400) timeStr = Math.floor(diff/86400) + 'd ago';
                                    
                                    html += `
                                        <a href="${url}" class="notif-item flex items-start gap-3 p-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors ${bgClass}" data-id="${n.id}" data-unread="${isUnread}">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                                                <i data-lucide="${icon}" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">${n.title}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">${n.message}</p>
                                                <p class="text-[11px] text-slate-400 mt-1 font-medium">${timeStr}</p>
                                            </div>
                                            ${dot}
                                        </a>
                                    `;
                                });
                                notifList.innerHTML = html;
                                if (window.lucide) lucide.createIcons();
                                
                                document.querySelectorAll('.notif-item').forEach(el => {
                                    el.addEventListener('click', function() {
                                        if (this.dataset.unread === 'true') {
                                            fetch((window.SecurePollBase || '') + '/notifications/markRead', {
                                                method: 'POST',
                                                headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
                                                body: 'id=' + this.dataset.id + '&csrf_token=' + (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
                                            });
                                        }
                                    });
                                });
                            }
                        }).catch(() => {
                            if (notifList) notifList.innerHTML = '<div class="p-4 text-center text-xs text-rose-500">Failed to load notifications</div>';
                        });
                }
                
                fetchUnreadCount();
                setInterval(fetchUnreadCount, 60000);
            }
        });
    </script>
</body>
</html>
