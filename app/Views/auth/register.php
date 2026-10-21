<?php
use app\Helpers\Security;

$pageTitle = 'Register - SecurePoll';
$basePrefix = strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '';
ob_start();
?>

<div class="w-full">
    <!-- Glassmorphism Card -->
    <div class="relative bg-white/80 dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/10 rounded-xl shadow dark:shadow-2xl overflow-hidden group">

        
        <div class="relative p-8 sm:p-10">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-3 tracking-tight">Create Account</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Join SecurePoll today and start creating polls</p>
            </div>

            <div id="alertBox" class="hidden mb-8 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md"></div>

            <!-- Step 1: Registration Form -->
            <form id="registerForm" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                
                <div class="space-y-3 mb-6">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Account Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center p-3 cursor-pointer rounded-xl border border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/60 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all has-[:checked]:bg-teal-50/50 dark:has-[:checked]:bg-teal-500/10 has-[:checked]:border-teal-300 dark:has-[:checked]:border-teal-500/40">
                            <input type="radio" name="account_type" value="personal" class="peer sr-only" checked>
                            <div class="w-4 h-4 rounded-full border border-slate-400 dark:border-slate-600 peer-checked:border-teal-600 dark:peer-checked:border-teal-500 mr-3 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-teal-600 dark:bg-teal-500 scale-0 peer-checked:scale-100 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 peer-checked:text-teal-900 dark:peer-checked:text-teal-100">Personal</span>
                        </label>
                        <div id="orgAccountOption" role="button" aria-disabled="true" tabindex="0" title="Organization accounts are coming soon." class="relative flex items-center p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/40 opacity-60 cursor-not-allowed select-none transition-all">
                            <input type="radio" name="account_type" value="organization" id="account_type_org" class="peer sr-only" disabled aria-disabled="true" tabindex="-1">
                            <div class="w-4 h-4 rounded-full border border-slate-400 dark:border-slate-600 mr-2.5 flex items-center justify-center shrink-0">
                                <div class="w-2 h-2 rounded-full bg-slate-400 scale-0"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">Organization</span>
                            <span class="absolute -top-2.5 right-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold tracking-wide bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700 shadow-sm">Coming Soon</span>
                        </div>
                    </div>
                </div>

                <div id="orgFields" class="space-y-6 hidden">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Organization Type</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="graduation-cap" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                            </div>
                            <select name="org_type" id="org_type" 
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner appearance-none">
                                <option value="">Select a type...</option>
                                <option value="college">College / University</option>
                                <option value="school">School</option>
                                <option value="company">Company</option>
                                <option value="community">Community / Non-profit</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label id="orgNameLabel" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Organization Name</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="building" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                            </div>
                            <input type="text" name="org_name" id="org_name" placeholder="Enter organization name" 
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner">
                        </div>
                    </div>
                    <hr class="border-slate-200 dark:border-slate-700/50">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Owner / Admin Details</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" id="nameLabel">Full Name</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                        </div>
                        <input type="text" name="name" required placeholder="John Doe" 
                            class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner">
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" id="emailLabel">Email address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                        </div>
                        <input type="email" name="email" id="email" required placeholder="name@company.com" 
                            class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner">
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-500 dark:group-focus-within:text-teal-400 transition-colors"></i>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••" minlength="8"
                            class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl pl-11 pr-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 transition-all shadow-inner">
                    </div>
                    <p class="text-xs text-slate-500 mt-2 ml-1">Must be at least 8 characters long</p>
                </div>
                
                <button type="submit" id="submitBtn" 
                    class="w-full relative overflow-hidden bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-teal-500/20 hover:shadow-teal-500/25 disabled:opacity-50 disabled:cursor-not-allowed group">

                    <span id="btnText" class="relative z-10">Sign Up</span>
                    <i data-lucide="loader-2" id="spinner" class="w-5 h-5 animate-spin hidden relative z-10"></i>
                </button>
            </form>

            <!-- Step 2: OTP Form -->
            <form id="otpForm" class="hidden space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                <input type="hidden" name="email" id="otpEmail">
                <input type="hidden" name="purpose" value="registration">
                
                <div class="text-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Verify Your Email</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">We sent a 6-digit verification passcode to</p>
                    <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-teal-950/40 border border-slate-200 dark:border-teal-900/50 text-xs">
                        <span id="displayEmail" class="font-medium text-slate-800 dark:text-slate-200"></span>
                        <button type="button" id="editEmailBtn" class="text-teal-600 dark:text-teal-400 hover:underline font-semibold" title="Change email address">Change</button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="otpInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 text-center">Enter 6-Digit Passcode</label>
                    <input type="text" name="otp" id="otpInput" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="000000" 
                        class="w-full bg-white dark:bg-[#071416] border-2 border-slate-200 dark:border-teal-900/60 rounded-xl px-4 py-3.5 text-center text-3xl font-mono tracking-[0.5em] text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-700 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all shadow-inner">
                    <p class="text-[11px] text-center text-slate-400 dark:text-slate-500">Passcode expires in 10 minutes</p>
                </div>
                
                <button type="submit" id="otpSubmitBtn" 
                    class="w-full relative overflow-hidden bg-teal-600 hover:bg-teal-500 dark:bg-teal-600 dark:hover:bg-teal-500 text-white font-semibold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-teal-500/20 disabled:opacity-50 disabled:cursor-not-allowed group">

                    <span id="otpBtnText" class="relative z-10">Verify & Complete</span>
                    <i data-lucide="loader-2" id="otpSpinner" class="w-4 h-4 animate-spin hidden relative z-10"></i>
                </button>
                
                <div class="text-center mt-4">
                    <button type="button" id="resendOtpBtn" class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i data-lucide="refresh-cw" id="resendSpinner" class="w-3.5 h-3.5 hidden animate-spin"></i>
                        <span id="resendBtnText">Didn't receive a code? Resend</span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-200 dark:border-slate-700/50 px-8 py-5 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Already have an account? 
                <a href="<?php echo $basePrefix; ?>/login" class="font-medium text-teal-600 dark:text-teal-400 hover:text-teal-500 dark:hover:text-teal-300 transition-colors">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const otpForm = document.getElementById('otpForm');
    const alertBox = document.getElementById('alertBox');
    
    // Register Elements
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('spinner');
    
    // OTP Elements
    const otpSubmitBtn = document.getElementById('otpSubmitBtn');
    const otpBtnText = document.getElementById('otpBtnText');
    const otpSpinner = document.getElementById('otpSpinner');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const resendBtnText = document.getElementById('resendBtnText');
    const resendSpinner = document.getElementById('resendSpinner');
    let resendCountdownTimer = null;

    function showAlert(message, isSuccess = false) {
        alertBox.textContent = message;
        alertBox.className = `mb-8 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border ${isSuccess ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20' : 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/20'}`;
        alertBox.classList.remove('hidden');
        
        // Add icon
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', isSuccess ? 'check-circle' : 'alert-circle');
        icon.className = 'w-5 h-5 shrink-0 mt-0.5';
        alertBox.prepend(icon);
        lucide.createIcons();
    }

    function hideAlert() {
        alertBox.classList.add('hidden');
    }

    function startResendCooldown(seconds = 60) {
        if (resendCountdownTimer) clearInterval(resendCountdownTimer);
        if (!resendOtpBtn || !resendBtnText) return;

        resendOtpBtn.disabled = true;
        if (resendSpinner) resendSpinner.classList.add('hidden');
        
        let remaining = seconds;
        resendBtnText.textContent = `Resend available in ${remaining}s`;
        
        resendCountdownTimer = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(resendCountdownTimer);
                resendCountdownTimer = null;
                resendOtpBtn.disabled = false;
                resendBtnText.textContent = "Didn't receive a code? Resend";
            } else {
                resendBtnText.textContent = `Resend available in ${remaining}s`;
            }
        }, 1000);
    }

    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', () => {
            if (resendOtpBtn.disabled) return;
            hideAlert();

            const email = document.getElementById('otpEmail').value;
            const csrfToken = otpForm.querySelector('input[name="csrf_token"]')?.value || '';

            if (!email) {
                showAlert('Missing email for verification code resend.');
                return;
            }

            resendOtpBtn.disabled = true;
            if (resendSpinner) resendSpinner.classList.remove('hidden');
            resendBtnText.textContent = 'Sending new code...';

            const payload = new FormData();
            payload.append('csrf_token', csrfToken);
            payload.append('email', email);
            payload.append('purpose', 'registration');

            fetch('/SecurePoll/public/resend-otp', {
                method: 'POST',
                body: payload
            })
            .then(res => res.json().then(data => ({ status: res.status, data })))
            .then(({ status, data }) => {
                if (status === 200 && data.success) {
                    showAlert(data.message || 'A new verification code has been sent to your email.', true);
                    startResendCooldown(60);
                } else if (status === 429) {
                    showAlert(data.message || 'Please wait before requesting another code.');
                    startResendCooldown(data.retry_after || 60);
                } else {
                    showAlert(data.message || 'Failed to resend code.');
                    resendOtpBtn.disabled = false;
                    if (resendSpinner) resendSpinner.classList.add('hidden');
                    resendBtnText.textContent = "Didn't receive a code? Resend";
                }
            })
            .catch(err => {
                showAlert('A network error occurred. Please check your connection.');
                resendOtpBtn.disabled = false;
                if (resendSpinner) resendSpinner.classList.add('hidden');
                resendBtnText.textContent = "Didn't receive a code? Resend";
            });
        });
    }

    // Account Type Option Handling
    const orgAccountOption = document.getElementById('orgAccountOption');
    if (orgAccountOption) {
        const notifyOrgComingSoon = (e) => {
            if (e) e.preventDefault();
            showAlert('Organization accounts are coming soon.');
            const personalRadio = document.querySelector('input[name="account_type"][value="personal"]');
            if (personalRadio) personalRadio.checked = true;
        };
        orgAccountOption.addEventListener('click', notifyOrgComingSoon);
        orgAccountOption.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                notifyOrgComingSoon(e);
            }
        });
    }

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();
        
        const selectedType = document.querySelector('input[name="account_type"]:checked')?.value || 'personal';
        if (selectedType === 'organization') {
            showAlert('Organization accounts are coming soon.', false);
            return;
        }

        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-busy', 'true');
        const btnText = document.getElementById('btnText');
        if (btnText) btnText.textContent = 'Creating account...';
        submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.remove('hidden'));
        
        const formData = new FormData(registerForm);
        
        // Telemetry signals
        formData.append('screen_resolution', (window.screen ? `${window.screen.width}x${window.screen.height}` : ''));
        try {
            formData.append('timezone', Intl.DateTimeFormat().resolvedOptions().timeZone || '');
        } catch (e) {}
        formData.append('language', navigator.language || navigator.userLanguage || '');

        fetch('<?php echo $basePrefix; ?>/register', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Show OTP form with animation
                registerForm.classList.add('hidden');
                otpForm.classList.remove('hidden');
                otpForm.classList.add('animate-fade-in-up');
                
                const userEmail = document.getElementById('email').value;
                document.getElementById('otpEmail').value = userEmail;
                document.getElementById('displayEmail').textContent = userEmail;
                
                showAlert('OTP sent to your email. Please check your inbox.', true);
                startResendCooldown(60);
            } else {
                showAlert(data.message);
            }
        })
        .catch(err => {
            showAlert('A network error occurred. Please check your connection.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.removeAttribute('aria-busy');
            if (btnText) btnText.textContent = 'Sign Up';
            submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.add('hidden'));
        });
    });

    otpForm.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();
        
        otpSubmitBtn.disabled = true;
        otpSubmitBtn.setAttribute('aria-busy', 'true');
        const otpBtnText = document.getElementById('otpBtnText');
        if (otpBtnText) otpBtnText.textContent = 'Verifying...';
        otpSubmitBtn.querySelectorAll('.animate-spin, #otpSpinner').forEach(el => el.classList.remove('hidden'));
        
        const formData = new FormData(otpForm);
        
        fetch('/SecurePoll/public/verify-otp', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('Registration successful! Redirecting...', true);
                setTimeout(() => {
                    window.location.href = '/SecurePoll/public/polls';
                }, 1000);
            } else {
                showAlert(data.message);
            }
        })
        .catch(err => {
            showAlert('A network error occurred. Please check your connection.');
        })
        .finally(() => {
            otpSubmitBtn.disabled = false;
            otpSubmitBtn.removeAttribute('aria-busy');
            if (otpBtnText) otpBtnText.textContent = 'Verify & Complete';
            otpSubmitBtn.querySelectorAll('.animate-spin, #otpSpinner').forEach(el => el.classList.add('hidden'));
        });
    });

    // Edit email / Back to step 1
    const editEmailBtn = document.getElementById('editEmailBtn');
    if (editEmailBtn) {
        editEmailBtn.addEventListener('click', () => {
            otpForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            hideAlert();
            const emailInput = document.getElementById('email');
            if (emailInput) emailInput.focus();
        });
    }

    // OTP Input auto-sanitization & paste formatting
    const otpInput = document.getElementById('otpInput');
    if (otpInput) {
        otpInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
        otpInput.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text');
            const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
            otpInput.value = cleanDigits;
            if (cleanDigits.length === 6) {
                // Auto focus submit
                otpSubmitBtn.focus();
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/guest.php';
?>
