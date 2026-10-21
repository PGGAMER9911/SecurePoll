<?php
namespace app\Services;

use app\Models\NotificationPreference;
use app\Models\User;
use app\Constants\NotificationTypes;

class NotificationPreferenceService {
    private $preferenceModel;
    private $userModel;

    public function __construct() {
        $this->preferenceModel = new NotificationPreference();
        $this->userModel = new User();
    }

    public function getPreferences($userId) {
        $preset = $this->preferenceModel->getPreset($userId);
        $customPrefs = $this->preferenceModel->getForUser($userId);
        
        $categories = NotificationTypes::getCategories();
        $allTypes = [];
        foreach ($categories as $cat => $types) {
            foreach ($types as $type) {
                $allTypes[] = $type;
            }
        }
        
        $result = ['preset' => $preset, 'settings' => []];
        $defaults = $this->getPresetDefaults($preset);
        
        foreach ($allTypes as $type) {
            if ($preset === 'Custom' && isset($customPrefs[$type])) {
                $result['settings'][$type] = $customPrefs[$type];
            } else {
                $result['settings'][$type] = [
                    'in_app' => in_array($type, $defaults['in_app']),
                    'email' => in_array($type, $defaults['email'])
                ];
            }
        }
        
        return $result;
    }

    public function setPreset($userId, $preset) {
        $presetMap = [
            'basic' => 'Standard',
            'advanced' => 'Custom',
            'minimal' => 'Minimal',
            'standard' => 'Standard',
            'all' => 'All',
            'custom' => 'Custom'
        ];
        $normalized = $presetMap[strtolower((string)$preset)] ?? $preset;
        $valid = ['Minimal', 'Standard', 'All', 'Custom'];
        if (!in_array($normalized, $valid)) return false;
        
        return $this->preferenceModel->setPreset($userId, $normalized);
    }

    public function updatePreference($userId, $type, $inApp, $email) {
        // Switch to custom preset automatically if they change something manually
        $currentPreset = $this->preferenceModel->getPreset($userId);
        if ($currentPreset !== 'Custom') {
            // First, copy current preset defaults to DB so other untouched settings don't flip
            $this->savePresetToCustom($userId, $currentPreset);
            $this->preferenceModel->setPreset($userId, 'Custom');
        }
        
        return $this->preferenceModel->set($userId, $type, $inApp, $email);
    }
    
    public function getEmailPreferences($userId) {
        $rows = $this->preferenceModel->getForUser($userId);
        return [
            'global' => isset($rows['global']['email']) ? (bool)$rows['global']['email'] : true,
            'invitations' => isset($rows['invitations']['email']) ? (bool)$rows['invitations']['email'] : true,
            'reminders' => isset($rows['reminders']['email']) ? (bool)$rows['reminders']['email'] : true,
            'updates' => isset($rows['updates']['email']) ? (bool)$rows['updates']['email'] : true,
            'results' => isset($rows['results']['email']) ? (bool)$rows['results']['email'] : true,
        ];
    }

    public function updateEmailPreferences($userId, array $prefs) {
        $keys = ['global', 'invitations', 'reminders', 'updates', 'results'];
        foreach ($keys as $k) {
            if (array_key_exists($k, $prefs)) {
                $emailVal = (bool)$prefs[$k];
                $existing = $this->preferenceModel->getForUserAndType($userId, $k);
                $inAppVal = isset($existing['in_app']) ? (bool)$existing['in_app'] : true;
                $this->preferenceModel->set($userId, $k, $inAppVal, $emailVal);
            }
        }
        return true;
    }

    public function isMandatorySecurityType($type) {
        $mandatory = [
            NotificationTypes::PASSWORD_RESET,
            NotificationTypes::PASSWORD_CHANGED,
            NotificationTypes::SECURITY_ALERT,
            NotificationTypes::EMAIL_VERIFICATION,
            NotificationTypes::LOGIN_NEW_DEVICE,
            'otp',
            'security',
            'password_reset'
        ];
        return in_array($type, $mandatory, true);
    }

    public function mapTypeToCategoryKey($type) {
        $map = [
            // Invitations
            NotificationTypes::NEW_INVITATION => 'invitations',
            NotificationTypes::INVITATION_ACCEPTED => 'invitations',
            NotificationTypes::INVITATION_DECLINED => 'invitations',
            NotificationTypes::INVITATION_REVOKED => 'invitations',
            NotificationTypes::INVITATION_EXPIRED => 'invitations',
            'invitations' => 'invitations',

            // Reminders
            NotificationTypes::POLL_ENDING_SOON => 'reminders',
            'reminders' => 'reminders',

            // Updates
            NotificationTypes::POLL_UPDATED => 'updates',
            NotificationTypes::POLL_DELETED => 'updates',
            NotificationTypes::POLL_STARTED => 'updates',
            NotificationTypes::VOTING_AVAILABLE => 'updates',
            'updates' => 'updates',

            // Results
            NotificationTypes::POLL_ENDED => 'results',
            NotificationTypes::VOTING_CLOSED => 'results',
            'results' => 'results',
        ];

        return $map[$type] ?? null;
    }

    public function canSendInApp($userId, $type) {
        if ($this->isMandatorySecurityType($type)) {
            return true;
        }

        $preset = $this->preferenceModel->getPreset($userId);
        if ($preset === 'Custom') {
            $pref = $this->preferenceModel->getForUserAndType($userId, $type);
            if ($pref && isset($pref['in_app'])) {
                return (bool)$pref['in_app'];
            }
        }

        $defaults = $this->getPresetDefaults($preset);
        return in_array($type, $defaults['in_app'] ?? [], true);
    }

    public function canSendEmail($userId, $type) {
        // 1. Mandatory account security emails are ALWAYS permitted
        if ($this->isMandatorySecurityType($type)) {
            return true;
        }

        // 2. Check Global email preference
        $emailPrefs = $this->getEmailPreferences($userId);
        if (!$emailPrefs['global']) {
            return false;
        }

        // 3. Check Category preference if mapped
        $catKey = $this->mapTypeToCategoryKey($type);
        if ($catKey && isset($emailPrefs[$catKey])) {
            if (!$emailPrefs[$catKey]) {
                return false;
            }
            // If preset is not Custom, the enabled category setting permits sending
            $preset = $this->preferenceModel->getPreset($userId);
            if ($preset !== 'Custom') {
                return true;
            }
        }

        // 4. Custom preset type-specific override
        $preset = $this->preferenceModel->getPreset($userId);
        if ($preset === 'Custom') {
            $pref = $this->preferenceModel->getForUserAndType($userId, $type);
            if ($pref && isset($pref['email'])) {
                return (bool)$pref['email'];
            }
        }

        // 5. Default preset check
        $defaults = $this->getPresetDefaults($preset);
        return in_array($type, $defaults['email'] ?? [], true);
    }

    private function savePresetToCustom($userId, $preset) {
        $defaults = $this->getPresetDefaults($preset);
        $categories = NotificationTypes::getCategories();
        foreach ($categories as $cat => $types) {
            foreach ($types as $type) {
                $inApp = in_array($type, $defaults['in_app']);
                $email = in_array($type, $defaults['email']);
                $this->preferenceModel->set($userId, $type, $inApp, $email);
            }
        }
    }

    private function getPresetDefaults($preset) {
        $all = [];
        foreach (NotificationTypes::getCategories() as $cat => $types) {
            foreach ($types as $type) {
                $all[] = $type;
            }
        }
        
        if ($preset === 'All') {
            return ['in_app' => $all, 'email' => $all];
        }
        
        if ($preset === 'Minimal') {
            $minimal = [
                NotificationTypes::POLL_CREATED,
                NotificationTypes::POLL_ENDED,
                NotificationTypes::SECURITY_ALERT,
                NotificationTypes::PASSWORD_CHANGED,
                NotificationTypes::PASSWORD_RESET,
                NotificationTypes::NEW_INVITATION
            ];
            return ['in_app' => $minimal, 'email' => $minimal];
        }
        
        // Standard
        $standard = [
            NotificationTypes::POLL_CREATED,
            NotificationTypes::POLL_STARTED,
            NotificationTypes::POLL_UPDATED,
            NotificationTypes::POLL_DELETED,
            NotificationTypes::POLL_ENDING_SOON,
            NotificationTypes::POLL_ENDED,
            NotificationTypes::VOTE_RECEIVED,
            NotificationTypes::MAX_VOTERS_REACHED,
            NotificationTypes::NEW_INVITATION,
            NotificationTypes::INVITATION_ACCEPTED,
            NotificationTypes::INVITATION_DECLINED,
            NotificationTypes::WELCOME,
            NotificationTypes::SECURITY_ALERT,
            NotificationTypes::PASSWORD_CHANGED,
            NotificationTypes::PASSWORD_RESET,
            NotificationTypes::POLL_SIGNIFICANT_ACTIVITY
        ];
        return ['in_app' => $standard, 'email' => $standard];
    }
}
