<?php 
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$pageTitle = 'Notification Settings'; 
$currentPreset = $preferences['preset'] ?? 'Standard';
$isAdvanced = in_array(strtolower((string)$currentPreset), ['custom', 'advanced'], true);
?>
<?php $basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
ob_start(); ?>

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Notification Settings</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage how and when you receive notifications from SecurePoll.</p>
    </div>

    <!-- Presets -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-white/5 p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Notification Presets</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <button onclick="setPreset('Standard')" class="text-left p-4 rounded-xl border-2 transition-all <?= !$isAdvanced ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/10' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' ?>">
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="shield-check" class="w-5 h-5 <?= !$isAdvanced ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' ?>"></i>
                    <span class="font-medium text-slate-900 dark:text-white">Basic (Recommended)</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-8">Only get notified for critical events, invitations, and when your polls end.</p>
            </button>
            <button onclick="setPreset('Custom')" class="text-left p-4 rounded-xl border-2 transition-all <?= $isAdvanced ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/10' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700' ?>">
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="sliders" class="w-5 h-5 <?= $isAdvanced ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' ?>"></i>
                    <span class="font-medium text-slate-900 dark:text-white">Advanced</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 pl-8">Customize every individual notification type exactly how you want it.</p>
            </button>
        </div>
    </div>

    <!-- Advanced Settings (Only visible if preset is advanced) -->
    <div id="advancedSettings" class="<?= !$isAdvanced ? 'hidden' : '' ?> bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-white/5 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/5">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Detailed Preferences</h3>
            <p class="text-sm text-slate-500 mt-1">Choose which notifications you receive and via which channels.</p>
        </div>
        
        <div class="divide-y divide-slate-100 dark:divide-white/5">
            <?php 
                // Group preferences by category
                $grouped = [];
                $titles = [
                    'POLL_CREATED' => 'Poll Created',
                    'POLL_STARTED' => 'Poll Started',
                    'POLL_ENDED' => 'Poll Ended',
                    'POLL_ENDING_SOON' => 'Poll Ending Soon',
                    'VOTE_RECEIVED' => 'New Vote Received',
                    'NEW_INVITATION' => 'New Invitation',
                    'INVITATION_ACCEPTED' => 'Invitation Accepted',
                    'VOTING_AVAILABLE' => 'Voting Available'
                ];
                
                $settingsList = $preferences['settings'] ?? [];
                foreach ($settingsList as $type => $setting) {
                    $cat = 'General';
                    if (strpos($type, 'POLL_') === 0) $cat = 'Poll Lifecycle';
                    if (strpos($type, 'VOTE_') === 0 || $type === 'VOTING_AVAILABLE') $cat = 'Voting Activity';
                    if (strpos($type, 'INVITATION_') === 0 || $type === 'NEW_INVITATION') $cat = 'Invitations';
                    
                    if (!isset($grouped[$cat])) $grouped[$cat] = [];
                    $grouped[$cat][] = [
                        'notification_type' => $type,
                        'in_app' => !empty($setting['in_app']),
                        'email' => !empty($setting['email'])
                    ];
                }
            ?>
            
            <?php foreach ($grouped as $cat => $prefs): ?>
                <div class="p-6">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4"><?= $cat ?></h4>
                    <div class="space-y-4">
                        <?php foreach ($prefs as $pref): ?>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white"><?= $titles[$pref['notification_type']] ?? $pref['notification_type'] ?></p>
                                </div>
                                <div class="flex items-center gap-4 shrink-0">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" 
                                               onchange="updatePref('<?= $pref['notification_type'] ?>', 'in_app', this.checked)"
                                               <?= $pref['in_app'] ? 'checked' : '' ?>>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">In-App</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" 
                                               onchange="updatePref('<?= $pref['notification_type'] ?>', 'email', this.checked)"
                                               <?= $pref['email'] ? 'checked' : '' ?>>
                                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Email</span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    function setPreset(preset) {
        fetch((window.SecurePollBase || '') + '/notifications/settings/preset', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'preset=' + preset + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
        }).then(r => r.json()).then(res => {
            if (res.success) {
                window.location.reload();
            }
        });
    }

    function updatePref(type, channel, value) {
        fetch((window.SecurePollBase || '') + '/notifications/settings/update', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'type=' + type + '&channel=' + channel + '&value=' + (value ? 1 : 0) + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
        }).then(r => r.json()).then(res => {
            if (!res.success) {
                alert(res.message);
                window.location.reload(); // Reset UI on fail
            }
        });
    }
</script>

<?php 
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean(); 
require_once __DIR__ . '/../layouts/app.php'; 
?>
