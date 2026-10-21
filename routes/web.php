<?php
use app\Core\Router;
use app\Controllers\AuthController;
use app\Controllers\PollController;
use app\Controllers\DashboardController;
use app\Controllers\ProfileController;
use app\Controllers\SettingsController;
use app\Controllers\NotificationController;
use app\Controllers\PublicController;

// Public Website
Router::get('/', [PublicController::class, 'home']);
Router::get('/about', [PublicController::class, 'about']);
Router::get('/docs', [PublicController::class, 'docs']);
Router::get('/documentation', [PublicController::class, 'docs']);
Router::get('/pricing', [PublicController::class, 'pricing']);
Router::get('/contact', [PublicController::class, 'contact']);

// Auth
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'showRegister']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Router::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Router::post('/resend-otp', [AuthController::class, 'resendOtp']);
Router::post('/logout', [AuthController::class, 'logout']);
Router::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Router::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Router::get('/reset-password', [AuthController::class, 'showResetPassword']);
Router::post('/reset-password', [AuthController::class, 'resetPassword']);

// Dashboard
Router::get('/dashboard', [DashboardController::class, 'index']);
Router::get('/dashboard/analytics', [DashboardController::class, 'analytics']);
Router::get('/invitations', [DashboardController::class, 'invitations']);
Router::post('/invitations/accept', [DashboardController::class, 'acceptInvitation']);
Router::post('/invitations/decline', [DashboardController::class, 'declineInvitation']);

// Profile
if (class_exists(ProfileController::class)) {
    Router::get('/profile', [ProfileController::class, 'show']);
}

// Settings
Router::get('/settings', [SettingsController::class, 'index']);
Router::post('/settings/theme', [SettingsController::class, 'updateTheme']);
Router::post('/settings/updateTheme', [SettingsController::class, 'updateTheme']);

// Polls
Router::get('/polls', [PollController::class, 'index']);
Router::get('/polls/my-polls', [PollController::class, 'myPolls']);
Router::get('/polls/create', [PollController::class, 'create']);
Router::post('/polls/store', [PollController::class, 'store']);
Router::get('/polls/show', [PollController::class, 'show']);
Router::post('/polls/vote', [PollController::class, 'vote']);
Router::get('/polls/results', [PollController::class, 'results']);
Router::get('/polls/manage', [PollController::class, 'manage']);
Router::post('/polls/importCsv', [PollController::class, 'importCsv']);
Router::post('/polls/delete', [PollController::class, 'delete']);
Router::post('/polls/update', [PollController::class, 'update']);
Router::get('/polls/voterActivity', [PollController::class, 'voterActivity']);

// Private Poll Invitation Routes
Router::get('/polls/searchUsers', [PollController::class, 'searchUsers']);
Router::post('/polls/inviteUser', [PollController::class, 'inviteUser']);
Router::get('/polls/getInvitations', [PollController::class, 'getInvitations']);
Router::post('/polls/revokeInvitation', [PollController::class, 'revokeInvitation']);
// Settings Profile and Password
Router::post('/settings/profile', [SettingsController::class, 'updateProfile']);
Router::post('/settings/password', [SettingsController::class, 'updatePassword']);
Router::post('/settings/email-preferences', [SettingsController::class, 'updateEmailPreferences']);

// Notifications
Router::get('/notifications', [NotificationController::class, 'index']);
Router::get('/notifications/latest', [NotificationController::class, 'latest']);
Router::get('/notifications/unreadCount', [NotificationController::class, 'unreadCount']);
Router::get('/settings/notifications', [NotificationController::class, 'settings']);
Router::post('/notifications/settings/update', [NotificationController::class, 'updatePreference']);
Router::post('/notifications/settings/preset', [NotificationController::class, 'updatePreset']);
Router::post('/notifications/markRead', [NotificationController::class, 'markRead']);
Router::post('/notifications/markAllRead', [NotificationController::class, 'markAllRead']);
Router::post('/notifications/delete', [NotificationController::class, 'delete']);
Router::post('/notifications/clearAll', [NotificationController::class, 'clearAll']);

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = str_replace('/SecurePoll/public', '', $url);
Router::dispatch($url);
