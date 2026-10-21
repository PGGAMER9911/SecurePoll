<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\Session;
use app\Helpers\Security;
use app\Models\User;

class SettingsController extends Controller {
    public function __construct() {
        // Auth check moved to specific methods so updateTheme can support both authenticated and guest preferences
    }

    public function index() {
        $this->requireAuth();
        $userModel = new User();
        $user = $userModel->findById(Session::get('user_id'));
        $theme = $user['theme'] ?? Session::get('theme') ?? 'system';
        if (!in_array($theme, ['dark', 'light', 'system'], true)) {
            $theme = 'system';
        }
        
        $emailPreferences = [
            'global' => true,
            'invitations' => true,
            'reminders' => true,
            'updates' => true,
            'results' => true
        ];
        try {
            $prefService = new \app\Services\NotificationPreferenceService();
            $emailPreferences = $prefService->getEmailPreferences(Session::get('user_id'));
        } catch (\Throwable $e) {
            error_log("SettingsController::index failed to load email preferences: " . $e->getMessage());
        }
        
        return $this->view('settings/index', [
            'theme' => $theme,
            'user' => $user,
            'emailPreferences' => $emailPreferences
        ]);
    }

    public function updateTheme() {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403);
        }

        $theme = $_POST['theme'] ?? '';
        if (!in_array($theme, ['dark', 'light', 'system'], true)) {
            return $this->json(['success' => false, 'message' => 'Invalid theme value.'], 400);
        }

        // 1. Synchronize Session & Cookie
        Session::set('theme', $theme);
        if (!headers_sent()) {
            setcookie('securepoll_theme', $theme, [
                'expires' => time() + 31536000,
                'path' => '/',
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }

        // 2. Synchronize Database if user is authenticated
        $userId = Session::get('user_id');
        if ($userId) {
            $userModel = new User();
            $success = $userModel->updateTheme($userId, $theme);
            if (!$success) {
                return $this->json(['success' => false, 'message' => 'Failed to update theme in database.'], 500);
            }
        }

        return $this->json(['success' => true, 'message' => 'Theme updated successfully.', 'theme' => $theme]);
    }

    public function updateProfile() {
        $this->requireAuthApi();
        $this->requireCsrfApi();

        $userId = Session::get('user_id');
        $name = Security::sanitize($_POST['name'] ?? '');
        $bio = Security::sanitize($_POST['bio'] ?? '');
        $college = Security::sanitize($_POST['college'] ?? '');
        $department = Security::sanitize($_POST['department'] ?? '');
        $course = Security::sanitize($_POST['course'] ?? '');
        $semesterRaw = trim($_POST['semester'] ?? '');
        $enrollmentId = Security::sanitize($_POST['enrollment_id'] ?? '');

        if (empty($name)) {
            return $this->json(['success' => false, 'message' => 'Name cannot be empty.'], 400);
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            return $this->json(['success' => false, 'message' => 'Name must be between 2 and 100 characters.'], 400);
        }

        $semester = null;
        if ($semesterRaw !== '') {
            if (!is_numeric($semesterRaw) || (int)$semesterRaw < 1 || (int)$semesterRaw > 12) {
                return $this->json(['success' => false, 'message' => 'Semester must be a valid number between 1 and 12.'], 400);
            }
            $semester = (int)$semesterRaw;
        }

        $userModel = new User();
        $updated = $userModel->updateProfile($userId, [
            'name' => $name,
            'bio' => $bio,
            'college' => $college,
            'department' => $department,
            'course' => $course,
            'semester' => $semester,
            'enrollment_id' => $enrollmentId
        ]);

        if ($updated) {
            Session::set('user_name', $name);
            return $this->json(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        return $this->json(['success' => false, 'message' => 'Failed to update profile.'], 500);
    }

    public function updatePassword() {
        $this->requireAuthApi();
        $this->requireCsrfApi();

        $userId = Session::get('user_id');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 8) {
            return $this->json(['success' => false, 'message' => 'New password must be at least 8 characters long.'], 400);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->json(['success' => false, 'message' => 'New password and confirmation do not match.'], 400);
        }

        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        // If the user already has a password set, require and verify the current password
        if (!empty($user['password_hash'])) {
            if (empty($currentPassword) || !password_verify($currentPassword, $user['password_hash'])) {
                return $this->json(['success' => false, 'message' => 'Current password is incorrect.'], 400);
            }
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $db = \app\Core\Database::getInstance()->getConnection();
        $db->beginTransaction();
        try {
            $userModel->updatePassword($userId, $newHash);

            // Update flag if user was required to set password
            if (!empty($user['must_set_password'])) {
                $db->prepare("UPDATE users SET must_set_password = 0, is_temporary = 0 WHERE id = :id")->execute(['id' => $userId]);
            }

            // Revoke other active sessions for this user, keeping current session intact
            $currentToken = Session::get('auth_token');
            $loginSessionModel = new \app\Models\LoginSession();
            if ($currentToken) {
                $loginSessionModel->revokeOtherUserSessions($userId, $currentToken);
            } else {
                $loginSessionModel->revokeAllUserSessions($userId);
            }

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            return $this->json(['success' => false, 'message' => 'Failed to update password. Please try again.'], 500);
        }

        // Record security & audit logs without exposing secrets
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $securityLog = new \app\Models\SecurityLog();
        $securityLog->log($userId, 'PASSWORD_CHANGED', 'Medium', 'Password updated from settings. Concurrent sessions revoked.', $ip, $ua);

        $auditLog = new \app\Models\AuditLog();
        $auditLog->log($userId, 'PASSWORD_CHANGED', 'users', $userId, null, null, $ip);

        return $this->json(['success' => true, 'message' => 'Password updated successfully. Other active sessions have been signed out.']);
    }

    public function updateEmailPreferences() {
        $this->requireAuthApi();
        $this->requireCsrfApi();

        return $this->json([
            'success' => false,
            'message' => 'Email notifications are not available yet. In-app notifications continue to work normally.'
        ], 400);
    }
}

