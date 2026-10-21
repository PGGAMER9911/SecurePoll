<?php
http_response_code(404);
$pageTitle = '404 - Page Not Found';
$pageHeader = '';
ob_start();
?>
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <!-- Glowing Error Icon -->
    <div class="relative mb-8">
        <div class="absolute inset-0 bg-indigo-500 rounded-full blur-2xl opacity-20 animate-pulse"></div>
        <div class="relative bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-300 dark:border-white/10 p-6 rounded-full shadow-2xl">
            <i data-lucide="compass" class="w-16 h-16 text-indigo-400"></i>
        </div>
    </div>
    
    <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-blue-500 mb-4 tracking-tight">404</h1>
    <h2 class="text-2xl font-semibold text-slate-900 dark:text-white mb-4">Page Not Found</h2>
    
    <p class="text-slate-400 max-w-md mx-auto mb-10 leading-relaxed">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Let's get you back on track.
    </p>
    
    <a href="/SecurePoll/public/polls" class="relative overflow-hidden bg-indigo-600 hover:bg-indigo-700 text-slate-900 dark:text-white font-semibold py-3.5 px-8 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 group">
        <span class="absolute right-0 w-8 h-32 -mt-12 transition-all duration-1000 transform translate-x-12 bg-white opacity-10 rotate-12 group-hover:-translate-x-48 ease"></span>
        <i data-lucide="home" class="w-5 h-5 relative z-10"></i>
        <span class="relative z-10 text-base">Back to Home</span>
    </a>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
