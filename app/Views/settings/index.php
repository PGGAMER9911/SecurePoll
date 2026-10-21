<?php
use app\Helpers\Security;
$pageTitle = 'Settings - SecurePoll';
$pageHeader = 'Settings';
ob_start();
$currentUser = $user ?? [];
$emailPrefs = $emailPreferences ?? [
    'global' => true,
    'invitations' => true,
    'reminders' => true,
    'updates' => true,
    'results' => true
];
?>

<style>
/* SecurePoll Custom Accessible Toggle Switch */
.sp-toggle {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    flex-shrink: 0;
}

.sp-toggle-input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

.sp-toggle-track {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    border-radius: 9999px;
    background-color: #dc2626 !important; /* OFF / Disabled: Red (bg-red-600) */
    transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25);
    vertical-align: middle;
}

.sp-toggle-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    border-radius: 9999px;
    background-color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35), 0 1px 2px rgba(0, 0, 0, 0.2);
    transform: translateX(0px); /* OFF: Thumb on left */
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ON / Enabled State: Green track (bg-green-600), Thumb on right */
.sp-toggle-input:checked + .sp-toggle-track {
    background-color: #16a34a !important; /* ON / Enabled: Green (bg-green-600) */
}

.sp-toggle-input:checked + .sp-toggle-track .sp-toggle-thumb {
    transform: translateX(20px) !important; /* ON: Thumb on right */
}

/* Accessible Keyboard Focus-Visible */
.sp-toggle-input:focus-visible + .sp-toggle-track {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35);
}

/* Distinct muted appearance when genuinely disabled by master switch */
.sp-toggle-input:disabled + .sp-toggle-track,
.sp-toggle-input[disabled] + .sp-toggle-track,
.sp-toggle.is-disabled .sp-toggle-track {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
}

.sp-toggle.is-disabled {
    cursor: not-allowed !important;
}
</style>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Application Settings</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage your profile, account security, and appearance preferences.</p>
</div>

<div class="space-y-8 max-w-4xl">

    <!-- Profile Information Section -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm dark:shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i> Profile Information
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Public details</span>
        </div>
        
        <form id="profileForm" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">

            <div id="profileAlert" class="hidden p-4 rounded-xl text-sm font-medium transition-all"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="profile_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="profile_name" name="name" required
                           value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                </div>

                <!-- Email (Read-only) -->
                <div>
                    <label for="profile_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="profile_email" readonly disabled
                           value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/60 bg-slate-100 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 cursor-not-allowed text-sm">
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Registered email cannot be modified directly.</p>
                </div>
            </div>

            <!-- Bio -->
            <div>
                <label for="profile_bio" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Bio
                </label>
                <textarea id="profile_bio" name="bio" rows="3"
                          placeholder="Tell the community a little about yourself..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm leading-relaxed"><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
            </div>

            <!-- Academic / Institutional Affiliation -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-500"></i> Academic & Institutional Details
                </h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="profile_college" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            College / Institute
                        </label>
                        <input type="text" id="profile_college" name="college"
                               value="<?php echo htmlspecialchars($currentUser['college'] ?? ''); ?>"
                               placeholder="e.g. Stanford University"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                    </div>

                    <div>
                        <label for="profile_department" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Department
                        </label>
                        <input type="text" id="profile_department" name="department"
                               value="<?php echo htmlspecialchars($currentUser['department'] ?? ''); ?>"
                               placeholder="e.g. Computer Science & Engineering"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                    </div>

                    <div>
                        <label for="profile_course" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Course / Degree
                        </label>
                        <input type="text" id="profile_course" name="course"
                               value="<?php echo htmlspecialchars($currentUser['course'] ?? ''); ?>"
                               placeholder="e.g. B.Tech / M.Sc"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="profile_semester" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Semester
                            </label>
                            <input type="number" id="profile_semester" name="semester" min="1" max="12"
                                   value="<?php echo htmlspecialchars($currentUser['semester'] ?? ''); ?>"
                                   placeholder="1-12"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                        </div>

                        <div>
                            <label for="profile_enrollment" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Enrollment ID
                            </label>
                            <input type="text" id="profile_enrollment" name="enrollment_id"
                                   value="<?php echo htmlspecialchars($currentUser['enrollment_id'] ?? ''); ?>"
                                   placeholder="ID / Roll #"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="profileSubmitBtn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-medium text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-500/20 transition-all">
                    <span id="profileBtnText">Save Profile</span>
                    <i id="profileSpinner" data-lucide="loader-2" class="w-4 h-4 animate-spin hidden"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm dark:shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="key" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i> Change Password
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Security</span>
        </div>
        
        <form id="passwordForm" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">

            <div id="passwordAlert" class="hidden p-4 rounded-xl text-sm font-medium transition-all"></div>

            <div class="space-y-4 max-w-lg">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Current Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password"
                           placeholder="At least 8 characters"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Confirm New Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password"
                           placeholder="Re-enter new password"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/60 dark:border-slate-700/50 flex items-start gap-3">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        Updating your password will immediately sign out all other active sessions across your browsers and devices. Your current session will remain active.
                    </p>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="passwordSubmitBtn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-medium text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-500/20 transition-all">
                    <span id="passwordBtnText">Update Password</span>
                    <i id="passwordSpinner" data-lucide="loader-2" class="w-4 h-4 animate-spin hidden"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Email Preferences Section -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm dark:shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="mail" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i> Email Notifications
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Delivery preferences</span>
        </div>
        
        <form id="emailPreferencesForm" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">

            <div id="emailAlert" class="hidden p-4 rounded-xl text-sm font-medium transition-all"></div>

            <!-- Global ON/OFF Switch -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-0.5 pr-4">
                    <label for="pref_global" class="text-base font-semibold text-slate-900 dark:text-white cursor-pointer">
                        Email Notifications
                    </label>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        Master switch. When turned off, you will not receive optional notification emails.
                    </p>
                </div>
                <label for="pref_global" class="sp-toggle relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle all email notifications">
                    <input type="checkbox" id="pref_global" name="global" value="1" role="switch" aria-checked="<?php echo !empty($emailPrefs['global']) ? 'true' : 'false'; ?>" class="sp-toggle-input" <?php echo !empty($emailPrefs['global']) ? 'checked' : ''; ?>>
                    <span class="sp-toggle-track">
                        <span class="sp-toggle-thumb"></span>
                    </span>
                </label>
            </div>

            <!-- Global Off Inactive Notice -->
            <div id="globalOffBanner" class="<?php echo !empty($emailPrefs['global']) ? 'hidden' : ''; ?> p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs sm:text-sm flex items-center gap-2.5">
                <i data-lucide="bell-off" class="w-4 h-4 shrink-0"></i>
                <span>Email notifications are currently paused. Category settings below are preserved but inactive until enabled.</span>
            </div>

            <!-- Category Specific Switches -->
            <div id="categoryContainer" class="space-y-4 transition-opacity duration-200 <?php echo empty($emailPrefs['global']) ? 'opacity-40 pointer-events-none' : ''; ?>">
                <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Notification Categories</h4>

                <!-- 1. Poll Invitations -->
                <div class="flex items-center justify-between py-2.5">
                    <div class="pr-4">
                        <label for="pref_invitations" class="text-sm font-medium text-slate-900 dark:text-slate-200 cursor-pointer flex items-center gap-2">
                            <i data-lucide="mail-plus" class="w-4 h-4 text-indigo-500"></i> Poll Invitations
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            When someone invites you to participate in a private or restricted poll.
                        </p>
                    </div>
                    <label for="pref_invitations" class="sp-toggle relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle poll invitations emails">
                        <input type="checkbox" id="pref_invitations" name="invitations" value="1" role="switch" aria-checked="<?php echo !empty($emailPrefs['invitations']) ? 'true' : 'false'; ?>" class="sp-toggle-input category-toggle" <?php echo !empty($emailPrefs['invitations']) ? 'checked' : ''; ?>>
                        <span class="sp-toggle-track">
                            <span class="sp-toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- 2. Poll Reminders -->
                <div class="flex items-center justify-between py-2.5 border-t border-slate-100 dark:border-slate-800/60">
                    <div class="pr-4">
                        <label for="pref_reminders" class="text-sm font-medium text-slate-900 dark:text-slate-200 cursor-pointer flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i> Poll Reminders
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Reminders for polls you are invited to or participating in before they close.
                        </p>
                    </div>
                    <label for="pref_reminders" class="sp-toggle relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle poll reminders emails">
                        <input type="checkbox" id="pref_reminders" name="reminders" value="1" role="switch" aria-checked="<?php echo !empty($emailPrefs['reminders']) ? 'true' : 'false'; ?>" class="sp-toggle-input category-toggle" <?php echo !empty($emailPrefs['reminders']) ? 'checked' : ''; ?>>
                        <span class="sp-toggle-track">
                            <span class="sp-toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- 3. Poll Updates -->
                <div class="flex items-center justify-between py-2.5 border-t border-slate-100 dark:border-slate-800/60">
                    <div class="pr-4">
                        <label for="pref_updates" class="text-sm font-medium text-slate-900 dark:text-slate-200 cursor-pointer flex items-center gap-2">
                            <i data-lucide="edit-3" class="w-4 h-4 text-emerald-500"></i> Poll Updates
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Important changes, schedule notices, and start announcements for relevant polls.
                        </p>
                    </div>
                    <label for="pref_updates" class="sp-toggle relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle poll updates emails">
                        <input type="checkbox" id="pref_updates" name="updates" value="1" role="switch" aria-checked="<?php echo !empty($emailPrefs['updates']) ? 'true' : 'false'; ?>" class="sp-toggle-input category-toggle" <?php echo !empty($emailPrefs['updates']) ? 'checked' : ''; ?>>
                        <span class="sp-toggle-track">
                            <span class="sp-toggle-thumb"></span>
                        </span>
                    </label>
                </div>

                <!-- 4. Results Published -->
                <div class="flex items-center justify-between py-2.5 border-t border-slate-100 dark:border-slate-800/60">
                    <div class="pr-4">
                        <label for="pref_results" class="text-sm font-medium text-slate-900 dark:text-slate-200 cursor-pointer flex items-center gap-2">
                            <i data-lucide="flag" class="w-4 h-4 text-purple-500"></i> Results Published
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Notifications when polls end and voting results become available to view.
                        </p>
                    </div>
                    <label for="pref_results" class="sp-toggle relative inline-flex items-center cursor-pointer shrink-0" aria-label="Toggle results published emails">
                        <input type="checkbox" id="pref_results" name="results" value="1" role="switch" aria-checked="<?php echo !empty($emailPrefs['results']) ? 'true' : 'false'; ?>" class="sp-toggle-input category-toggle" <?php echo !empty($emailPrefs['results']) ? 'checked' : ''; ?>>
                        <span class="sp-toggle-track">
                            <span class="sp-toggle-thumb"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Mandatory Security Notice -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 flex items-start gap-3">
                <i data-lucide="shield-check" class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5"></i>
                <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    <strong class="text-slate-900 dark:text-slate-200 font-semibold">Account Security Protection:</strong>
                    Essential security communications (such as password resets, verification OTPs, and new device login alerts) are always delivered and exempt from notification preferences. In-app notifications remain active regardless of your email settings.
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="emailSubmitBtn"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-medium text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-500/20 transition-all">
                    <span id="emailBtnText">Save Preferences</span>
                    <i id="emailSpinner" data-lucide="loader-2" class="w-4 h-4 animate-spin hidden"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Appearance Section -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm dark:shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="palette" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i> Appearance
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Interface theme</span>
        </div>
        
        <div class="p-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Theme</label>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Choose how SecurePoll looks to you.</p>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Dark Theme Option -->
                <button type="button" onclick="setTheme('dark')" data-settings-theme="dark" class="settings-theme-btn flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all <?php echo $theme === 'dark' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 shadow-sm dark:shadow-none' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm dark:hover:shadow-none'; ?>">
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="moon" class="w-5 h-5 theme-icon <?php echo $theme === 'dark' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400'; ?>"></i>
                        <span class="font-bold theme-label <?php echo $theme === 'dark' ? 'text-indigo-900 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-300'; ?>">Dark</span>
                    </div>
                    <span class="text-xs theme-desc <?php echo $theme === 'dark' ? 'text-indigo-700 dark:text-indigo-400/80' : 'text-slate-500 dark:text-slate-500'; ?>">Always use dark</span>
                </button>

                <!-- Light Theme Option -->
                <button type="button" onclick="setTheme('light')" data-settings-theme="light" class="settings-theme-btn flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all <?php echo $theme === 'light' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 shadow-sm dark:shadow-none' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm dark:hover:shadow-none'; ?>">
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="sun" class="w-5 h-5 theme-icon <?php echo $theme === 'light' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400'; ?>"></i>
                        <span class="font-bold theme-label <?php echo $theme === 'light' ? 'text-indigo-900 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-300'; ?>">Light</span>
                    </div>
                    <span class="text-xs theme-desc <?php echo $theme === 'light' ? 'text-indigo-700 dark:text-indigo-400/80' : 'text-slate-500 dark:text-slate-500'; ?>">Always use light</span>
                </button>
                
                <!-- System Theme Option -->
                <button type="button" onclick="setTheme('system')" data-settings-theme="system" class="settings-theme-btn flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all <?php echo $theme === 'system' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 shadow-sm dark:shadow-none' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-sm dark:hover:shadow-none'; ?>">
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="monitor" class="w-5 h-5 theme-icon <?php echo $theme === 'system' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400'; ?>"></i>
                        <span class="font-bold theme-label <?php echo $theme === 'system' ? 'text-indigo-900 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-300'; ?>">System</span>
                    </div>
                    <span class="text-xs theme-desc <?php echo $theme === 'system' ? 'text-indigo-700 dark:text-indigo-400/80' : 'text-slate-500 dark:text-slate-500'; ?>">Follow device theme</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function setTheme(newTheme) {
    if (window.SecurePollTheme) {
        window.SecurePollTheme.setTheme(newTheme);
    }
}

// Keep buttons in sync if theme changes via any other control
document.addEventListener('DOMContentLoaded', () => {
    if (window.SecurePollTheme) {
        window.SecurePollTheme.updateControls(window.SecurePollTheme.getTheme());
    }
    syncGlobalEmailUI();
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

// Profile Form Submit
document.getElementById('profileForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('profileAlert');
    const submitBtn = document.getElementById('profileSubmitBtn');
    const btnText = document.getElementById('profileBtnText');
    const spinner = document.getElementById('profileSpinner');

    alertBox.className = 'hidden';
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Saving...';

    const formData = new FormData(this);

    try {
        const response = await fetch('/SecurePoll/public/settings/profile', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20';
            alertBox.textContent = data.message || 'Profile updated successfully.';
            // Update any displayed name in the top bar if element exists
            const userDisplay = document.querySelector('[data-user-name]');
            if (userDisplay && formData.get('name')) {
                userDisplay.textContent = formData.get('name');
            }
        } else {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
            alertBox.textContent = data.message || 'Failed to update profile.';
        }
    } catch (err) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
        alertBox.textContent = 'A network error occurred. Please try again.';
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Save Profile';
    }
});

// Password Form Submit
document.getElementById('passwordForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('passwordAlert');
    const submitBtn = document.getElementById('passwordSubmitBtn');
    const btnText = document.getElementById('passwordBtnText');
    const spinner = document.getElementById('passwordSpinner');

    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    if (newPass.length < 8) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
        alertBox.textContent = 'New password must be at least 8 characters long.';
        return;
    }

    if (newPass !== confirmPass) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
        alertBox.textContent = 'New password and confirmation do not match.';
        return;
    }

    alertBox.className = 'hidden';
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Updating...';

    const formData = new FormData(this);

    try {
        const response = await fetch('/SecurePoll/public/settings/password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20';
            alertBox.textContent = data.message || 'Password updated successfully.';
            document.getElementById('passwordForm').reset();
        } else {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
            alertBox.textContent = data.message || 'Failed to update password.';
        }
    } catch (err) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
        alertBox.textContent = 'A network error occurred. Please try again.';
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Update Password';
    }
});

// Email Preferences Toggle & Submit
const globalToggle = document.getElementById('pref_global');
const catContainer = document.getElementById('categoryContainer');
const offBanner = document.getElementById('globalOffBanner');
const categoryInputs = document.querySelectorAll('.category-toggle');

function syncGlobalEmailUI() {
    if (!globalToggle || !catContainer) return;
    const isMasterOn = globalToggle.checked;
    
    // Update accessible state on master toggle
    globalToggle.setAttribute('aria-checked', isMasterOn ? 'true' : 'false');

    if (isMasterOn) {
        // Master ON: categories active, banner hidden
        catContainer.classList.remove('opacity-40', 'pointer-events-none');
        catContainer.removeAttribute('aria-disabled');
        if (offBanner) offBanner.classList.add('hidden');

        categoryInputs.forEach(input => {
            input.disabled = false;
            const parentToggle = input.closest('.sp-toggle');
            if (parentToggle) {
                parentToggle.classList.remove('is-disabled');
            }
        });
    } else {
        // Master OFF: categories visually muted / inactive, banner shown
        // Crucially, category values and saved selections remain untouched
        catContainer.classList.add('opacity-40', 'pointer-events-none');
        catContainer.setAttribute('aria-disabled', 'true');
        if (offBanner) offBanner.classList.remove('hidden');

        categoryInputs.forEach(input => {
            input.disabled = true;
            const parentToggle = input.closest('.sp-toggle');
            if (parentToggle) {
                parentToggle.classList.add('is-disabled');
            }
        });
    }
}

// Attach change listeners to update aria-checked dynamically
categoryInputs.forEach(input => {
    input.setAttribute('aria-checked', input.checked ? 'true' : 'false');
    input.addEventListener('change', function() {
        this.setAttribute('aria-checked', this.checked ? 'true' : 'false');
    });
});

globalToggle?.addEventListener('change', syncGlobalEmailUI);

document.getElementById('emailPreferencesForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('emailAlert');
    const submitBtn = document.getElementById('emailSubmitBtn');
    const btnText = document.getElementById('emailBtnText');
    const spinner = document.getElementById('emailSpinner');

    alertBox.className = 'hidden';
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Saving...';

    const formData = new FormData(this);
    formData.set('global', document.getElementById('pref_global')?.checked ? '1' : '0');
    formData.set('invitations', document.getElementById('pref_invitations')?.checked ? '1' : '0');
    formData.set('reminders', document.getElementById('pref_reminders')?.checked ? '1' : '0');
    formData.set('updates', document.getElementById('pref_updates')?.checked ? '1' : '0');
    formData.set('results', document.getElementById('pref_results')?.checked ? '1' : '0');

    try {
        const response = await fetch('/SecurePoll/public/settings/email-preferences', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20';
            alertBox.textContent = data.message || 'Email preferences saved successfully.';
        } else {
            alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
            alertBox.textContent = data.message || 'Failed to save email preferences.';
        }
    } catch (err) {
        alertBox.className = 'p-4 rounded-xl text-sm font-medium bg-rose-50 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20';
        alertBox.textContent = 'A network error occurred. Please try again.';
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Save Preferences';
        if (window.lucide) window.lucide.createIcons();
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
