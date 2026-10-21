<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

\app\Helpers\Env::load(__DIR__ . '/.env');

use app\Helpers\Session;

Session::init();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';

echo "====================================================================\n";
echo " SecurePoll: Settings Toggle UI Verification Test\n";
echo "====================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($cond, $msg) {
    global $passCount, $failCount;
    if ($cond) {
        echo "  [PASS] $msg\n";
        $passCount++;
    } else {
        echo "  [FAIL] $msg\n";
        $failCount++;
    }
}

function renderSettingsView($emailPrefsArray) {
    $emailPreferences = $emailPrefsArray;
    $theme = 'dark';
    $user = ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'];
    
    // We run in an isolated php process to ensure clean global state and layout rendering
    $code = '
        spl_autoload_register(function ($class) {
            $file = __DIR__ . "/" . str_replace("\\\\", "/", $class) . ".php";
            if (file_exists($file)) require_once $file;
        });
        \\app\\Helpers\\Env::load(__DIR__ . "/.env");
        \\app\\Helpers\\Session::init();
        $_SESSION["user_id"] = 1;
        $_SESSION["user_name"] = "Test User";
        $emailPreferences = ' . var_export($emailPrefsArray, true) . ';
        $theme = "dark";
        $user = ["id" => 1, "name" => "Test User", "email" => "test@example.com"];
        include __DIR__ . "/app/Views/settings/index.php";
        echo $content;
    ';
    
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $process = proc_open('d:\\xampp\\php\\php.exe', $descriptors, $pipes);
    fwrite($pipes[0], "<?php\n" . $code);
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
    
    return $out;
}

// Case 1: Master switch ON, mix of category ON and OFF
$htmlCase1 = renderSettingsView([
    'global' => true,
    'invitations' => true,
    'reminders' => false,
    'updates' => true,
    'results' => false
]);

echo "--- Test Group 1: Master Switch ON Markup & Structure ---\n";
assertCondition(strpos($htmlCase1, 'sp-toggle-track') !== false, 'sp-toggle-track class is present');
assertCondition(strpos($htmlCase1, 'sp-toggle-thumb') !== false, 'sp-toggle-thumb class is present');
assertCondition(strpos($htmlCase1, 'background-color: #16a34a !important') !== false, 'Green (#16a34a) mapped to checked ON track in CSS');
assertCondition(strpos($htmlCase1, 'background-color: #dc2626 !important') !== false, 'Red (#dc2626) mapped to unchecked OFF track in CSS');

// Master switch ON checks
assertCondition(preg_match('/id="pref_global"[^>]*role="switch"/', $htmlCase1) === 1, 'pref_global has role="switch"');
assertCondition(preg_match('/id="pref_global"[^>]*aria-checked="true"/', $htmlCase1) === 1, 'pref_global has aria-checked="true" when ON');
assertCondition(preg_match('/id="pref_global"[^>]*\schecked\b/', $htmlCase1) === 1, 'pref_global input has checked attribute when ON');

// Category switches checks (Master ON)
assertCondition(preg_match('/id="pref_invitations"[^>]*\schecked\b/', $htmlCase1) === 1, 'pref_invitations is checked when ON');
assertCondition(preg_match('/id="pref_invitations"[^>]*aria-checked="true"/', $htmlCase1) === 1, 'pref_invitations aria-checked="true" when ON');

assertCondition(preg_match('/id="pref_reminders"[^>]*\schecked\b/', $htmlCase1) === 0, 'pref_reminders is not checked when OFF');
assertCondition(preg_match('/id="pref_reminders"[^>]*aria-checked="false"/', $htmlCase1) === 1, 'pref_reminders aria-checked="false" when OFF');

assertCondition(preg_match('/id="pref_updates"[^>]*\schecked\b/', $htmlCase1) === 1, 'pref_updates is checked when ON');
assertCondition(preg_match('/id="pref_updates"[^>]*aria-checked="true"/', $htmlCase1) === 1, 'pref_updates aria-checked="true" when ON');

assertCondition(preg_match('/id="pref_results"[^>]*\schecked\b/', $htmlCase1) === 0, 'pref_results is not checked when OFF');
assertCondition(preg_match('/id="pref_results"[^>]*aria-checked="false"/', $htmlCase1) === 1, 'pref_results aria-checked="false" when OFF');

assertCondition(strpos($htmlCase1, 'id="globalOffBanner" class="hidden') !== false, 'globalOffBanner is hidden when master is ON');
assertCondition(strpos($htmlCase1, 'id="categoryContainer" class="space-y-4 transition-opacity duration-200 "') !== false, 'categoryContainer is not muted when master is ON');


// Case 2: Master switch OFF, category preferences preserved
$htmlCase2 = renderSettingsView([
    'global' => false,
    'invitations' => true,
    'reminders' => false,
    'updates' => true,
    'results' => false
]);

echo "\n--- Test Group 2: Master Switch OFF Markup & Preference Preservation ---\n";
assertCondition(preg_match('/id="pref_global"[^>]*\schecked\b/', $htmlCase2) === 0, 'pref_global input is unchecked when OFF');
assertCondition(preg_match('/id="pref_global"[^>]*aria-checked="false"/', $htmlCase2) === 1, 'pref_global has aria-checked="false" when OFF');

// Crucial: Category saved selections must remain preserved even when global is OFF
assertCondition(preg_match('/id="pref_invitations"[^>]*\schecked\b/', $htmlCase2) === 1, 'pref_invitations retains checked state when master is OFF');
assertCondition(preg_match('/id="pref_invitations"[^>]*aria-checked="true"/', $htmlCase2) === 1, 'pref_invitations retains aria-checked="true" when master is OFF');

assertCondition(preg_match('/id="pref_reminders"[^>]*\schecked\b/', $htmlCase2) === 0, 'pref_reminders retains unchecked state when master is OFF');
assertCondition(preg_match('/id="pref_reminders"[^>]*aria-checked="false"/', $htmlCase2) === 1, 'pref_reminders retains aria-checked="false" when master is OFF');

assertCondition(preg_match('/id="pref_updates"[^>]*\schecked\b/', $htmlCase2) === 1, 'pref_updates retains checked state when master is OFF');
assertCondition(preg_match('/id="pref_updates"[^>]*aria-checked="true"/', $htmlCase2) === 1, 'pref_updates retains aria-checked="true" when master is OFF');

assertCondition(preg_match('/id="pref_results"[^>]*\schecked\b/', $htmlCase2) === 0, 'pref_results retains unchecked state when master is OFF');
assertCondition(preg_match('/id="pref_results"[^>]*aria-checked="false"/', $htmlCase2) === 1, 'pref_results retains aria-checked="false" when master is OFF');

assertCondition(strpos($htmlCase2, 'id="globalOffBanner" class=" p-3.5') !== false && !preg_match('/id="globalOffBanner"[^>]*\bhidden\b/', $htmlCase2), 'globalOffBanner is visible (does not have hidden class) when master is OFF');
assertCondition(strpos($htmlCase2, 'id="categoryContainer" class="space-y-4 transition-opacity duration-200 opacity-40 pointer-events-none"') !== false, 'categoryContainer is visually inactive (opacity-40 pointer-events-none) when master is OFF');

echo "\n====================================================================\n";
echo " Test Results: $passCount Passed, $failCount Failed\n";
echo "====================================================================\n";

if ($failCount > 0) {
    exit(1);
}
