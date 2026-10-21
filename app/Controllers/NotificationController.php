<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Models\Notification;
use app\Services\NotificationPreferenceService;
use app\Helpers\Session;

class NotificationController extends Controller {
    private $notificationModel;
    private $preferenceService;

    public function __construct() {
        
        $this->notificationModel = new Notification();
        $this->preferenceService = new NotificationPreferenceService();
    }

    public function latest() {
        $this->requireAuthApi();
        $userId = Session::get('user_id');
        $notifications = $this->notificationModel->getForUser($userId, 5, 0, 'all');
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        return $this->json([
            'success' => true, 
            'data' => $notifications, 
            'unread_count' => $unreadCount
        ]);
    }

    public function unreadCount() {
        $this->requireAuthApi();
        $count = $this->notificationModel->getUnreadCount(Session::get('user_id'));
        return $this->json(['success' => true, 'count' => $count]);
    }

    public function index() {
        $this->requireAuth();
        $userId = Session::get('user_id');
        $filter = $_GET['filter'] ?? 'all';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $notifications = $this->notificationModel->getForUser($userId, $limit, $offset, $filter);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        return $this->view('notifications/index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'page' => $page,
            'unreadCount' => $unreadCount
        ]);
    }
    
    public function settings() {
        $this->requireAuth();
        $userId = Session::get('user_id');
        $preferences = $this->preferenceService->getPreferences($userId);
        
        return $this->view('notifications/settings', [
            'preferences' => $preferences
        ]);
    }

    public function updatePreference() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $type = $_POST['type'] ?? '';
        $inApp = isset($_POST['in_app']) ? (bool)$_POST['in_app'] : false;
        $email = isset($_POST['email']) ? (bool)$_POST['email'] : false;
        
        if (!$type) return $this->json(['success' => false, 'message' => 'Invalid type']);
        
        $this->preferenceService->updatePreference(Session::get('user_id'), $type, $inApp, $email);
        return $this->json(['success' => true]);
    }

    public function updatePreset() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $preset = $_POST['preset'] ?? '';
        if ($this->preferenceService->setPreset(Session::get('user_id'), $preset)) {
            return $this->json(['success' => true]);
        }
        return $this->json(['success' => false, 'message' => 'Invalid preset']);
    }

    public function markRead() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $id = $_POST['id'] ?? 0;
        $this->notificationModel->markAsRead($id, Session::get('user_id'));
        return $this->json(['success' => true]);
    }

    public function markAllRead() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $this->notificationModel->markAllAsRead(Session::get('user_id'));
        return $this->json(['success' => true]);
    }

    public function delete() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $id = $_POST['id'] ?? 0;
        $this->notificationModel->delete($id, Session::get('user_id'));
        return $this->json(['success' => true]);
    }

    public function clearAll() {
        $this->requireAuthApi();
        $this->requireCsrfApi();
        
        $this->notificationModel->clearAll(Session::get('user_id'));
        return $this->json(['success' => true]);
    }
}
