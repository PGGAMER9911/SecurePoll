<?php
use app\Helpers\Security;

$pageTitle = 'Forgot Password - SecurePoll';
$basePrefix = \app\Helpers\Url::base();
ob_start();
?>

<div class="w-full">
    <!-- Glassmorphism Card -->
    <div class="relative bg-white/80 dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-xl shadow dark:shadow-2xl overflow-hidden group">
        <div class="relative p-8 sm:p-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-teal-50 dark:bg-teal-500/10 border border-teal-100 dark:border-teal-500/20 mb-4">
                    <i data-lucide="key-round" class="w-7 h-7 text-teal-600 dark:text-teal-400"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">Forgot password?</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Enter your registered email address and we'll send you a link to reset your password.</p>
            </div>

            <div id="alertBox" class="hidden mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md"></div>

            <form id="forgotPasswordForm" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                        </div>
                        <input type="email" name="email" id="email" required placeholder="name@company.com" 
                            class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner">
                    </div>
                </div>
                
                <button type="submit" id="submitBtn" 
                    class="w-full relative overflow-hidden bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-teal-500/20 hover:shadow-teal-500/25 disabled:opacity-50 disabled:cursor-not-allowed group">
                    <span id="btnText" class="relative z-10">Send Reset Link</span>
                    <i data-lucide="loader-2" id="spinner" class="w-5 h-5 animate-spin hidden relative z-10"></i>
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-700/50 px-8 py-5 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Remember your password? 
                <a href="<?php echo $basePrefix; ?>/login" class="font-medium text-teal-600 dark:text-teal-400 hover:text-teal-500 dark:hover:text-teal-300 transition-colors">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forgotPasswordForm');
    const alertBox = document.getElementById('alertBox');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');

    function showAlert(message, isSuccess = false) {
        alertBox.textContent = message;
        alertBox.className = `mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border ${
            isSuccess 
                ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' 
                : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20'
        }`;
        alertBox.classList.remove('hidden');

        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', isSuccess ? 'check-circle' : 'alert-circle');
        icon.className = 'w-5 h-5 shrink-0 mt-0.5';
        alertBox.prepend(icon);
        if (window.lucide) lucide.createIcons();
    }

    function hideAlert() {
        alertBox.classList.add('hidden');
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();

        submitBtn.disabled = true;
        btnText.textContent = 'Sending link...';
        spinner.classList.remove('hidden');

        const formData = new FormData(form);

        fetch('<?php echo $basePrefix; ?>/forgot-password', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            if (data.success) {
                showAlert(data.message, true);
                form.reset();
            } else {
                showAlert(data.message || 'Unable to process your request. Please try again.');
            }
        })
        .catch(err => {
            showAlert('A network error occurred. Please check your connection.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = 'Send Reset Link';
            spinner.classList.add('hidden');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/guest.php';
?>
