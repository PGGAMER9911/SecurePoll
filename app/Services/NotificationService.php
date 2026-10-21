<?php
namespace app\Services;

use app\Models\Notification;
use app\Models\EmailQueue;
use app\Models\User;
use app\Models\Poll;
use app\Constants\NotificationTypes;

class NotificationService {
    private $notificationModel;
    private $preferenceService;
    private $emailQueue;
    private $userModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
        $this->preferenceService = new NotificationPreferenceService();
        $this->emailQueue = new EmailQueue();
        $this->userModel = new User();
    }

    public function notify($userId, $type, $title, $message, $metadata = []) {
        $inApp = $this->preferenceService->canSendInApp($userId, $type);
        $email = $this->preferenceService->canSendEmail($userId, $type);

        if ($inApp) {
            $data = [
                'user_id' => $userId,
                'type' => $type,
                'category' => $metadata['category'] ?? null,
                'title' => $title,
                'message' => $message,
                'icon' => $metadata['icon'] ?? null,
                'entity_type' => $metadata['entity_type'] ?? null,
                'entity_id' => $metadata['entity_id'] ?? null,
                'action_type' => $metadata['action_type'] ?? null,
                'group_key' => $metadata['group_key'] ?? null,
            ];
            $this->notificationModel->create($data);
        }

        if ($email) {
            $user = $this->userModel->findById($userId);
            if ($user && !empty($user['email'])) {
                // Determine email body
                $html = $this->buildEmailHtml($title, $message, $metadata);
                $this->emailQueue->enqueue($user['email'], $title, $html, "type:{$type}");
            }
        }
    }

    private function buildEmailHtml($title, $message, $metadata) {
        $baseUrl = \app\Helpers\Env::get('APP_URL', 'http://localhost/SecurePoll/public');
        $buttonHtml = '';
        $detailsHtml = '';

        if (!empty($metadata['invitation_token']) && !empty($metadata['entity_id'])) {
            $inviteUrl = "{$baseUrl}/polls/show?id={$metadata['entity_id']}&token={$metadata['invitation_token']}";
            $buttonHtml = "
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$inviteUrl}' style='display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-align: center;'>View & Vote in Poll</a>
                </div>
            ";
        } elseif (!empty($metadata['action_type']) && $metadata['action_type'] === 'view_invitations') {
            $inviteUrl = "{$baseUrl}/invitations";
            $buttonHtml = "
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$inviteUrl}' style='display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-align: center;'>View Invitations</a>
                </div>
            ";
        } elseif (!empty($metadata['entity_type']) && $metadata['entity_type'] === 'poll' && !empty($metadata['entity_id'])) {
            $pollUrl = "{$baseUrl}/polls/show?id={$metadata['entity_id']}";
            $buttonHtml = "
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$pollUrl}' style='display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; font-weight: 600; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-align: center;'>View Poll</a>
                </div>
            ";
        }

        if (!empty($metadata['expires_at'])) {
            $formattedExpiry = htmlspecialchars($metadata['expires_at']);
            $detailsHtml .= "<p style='font-size: 13px; color: #e11d48; margin-top: 10px; text-align: center;'>Deadline / Closes: <strong>{$formattedExpiry}</strong></p>";
        }

        $settingsUrl = "{$baseUrl}/settings";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; }
                .wrapper { background-color: #f8fafc; padding: 40px 20px; width: 100%; box-sizing: border-box; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
                .logo { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 24px; text-align: center; letter-spacing: -0.5px; }
                .logo span { color: #6366f1; }
                .header { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 16px; text-align: center; }
                .text { font-size: 15px; color: #475569; line-height: 1.6; text-align: center; margin-bottom: 24px; }
                .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #94a3b8; line-height: 1.5; }
                .footer a { color: #6366f1; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='wrapper'>
                <div class='container'>
                    <div class='logo'>Secure<span>Poll</span></div>
                    <div class='header'>" . htmlspecialchars($title) . "</div>
                    <div class='text'>" . nl2br(htmlspecialchars($message)) . "</div>
                    {$detailsHtml}
                    {$buttonHtml}
                    <div class='footer'>
                        <p>You received this email according to your SecurePoll notification preferences.</p>
                        <p>Manage your email notification preferences anytime in <a href='{$settingsUrl}'>Settings</a>.</p>
                        <p style='margin-top: 20px;'>&copy; " . date('Y') . " SecurePoll. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    // Specific Helpers
    
    public function notifyPollCreated($userId, $pollId, $title) {
        $this->notify($userId, NotificationTypes::POLL_CREATED, 'Poll Created', "Your poll '{$title}' was successfully created.", [
            'category' => 'Poll Activity',
            'icon' => 'plus-circle',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_poll'
        ]);
    }

    public function notifyVoteReceived($creatorId, $pollId, $pollTitle) {
        $groupKey = 'vote_received_poll_' . $pollId;
        $existing = $this->notificationModel->findByGroupKey($creatorId, $groupKey);
        $count = $existing ? $existing['group_count'] + 1 : 1;
        
        $message = $count === 1 
            ? "Someone voted on your poll '{$pollTitle}'." 
            : "{$count} people voted on your poll '{$pollTitle}'.";
            
        $this->notify($creatorId, NotificationTypes::VOTE_RECEIVED, 'New Vote Received', $message, [
            'category' => 'Poll Activity',
            'icon' => 'check-circle',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_poll',
            'group_key' => $groupKey
        ]);
    }
    
    public function notifyNewInvitation($inviteeUserId, $pollId, $pollTitle, $inviterName = null, $invitationToken = null, $expiresAt = null) {
        $message = !empty($inviterName) 
            ? "{$inviterName} has invited you to vote in '{$pollTitle}'." 
            : "You have been invited to vote in '{$pollTitle}'.";

        $this->notify($inviteeUserId, NotificationTypes::NEW_INVITATION, 'Poll Invitation: ' . $pollTitle, $message, [
            'category' => 'Invitations',
            'icon' => 'mail',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_invitations',
            'inviter_name' => $inviterName,
            'invitation_token' => $invitationToken,
            'expires_at' => $expiresAt
        ]);
    }

    public function notifyPollEndingSoon($userId, $pollId, $pollTitle, $hoursRemaining) {
        $this->notify($userId, NotificationTypes::POLL_ENDING_SOON, 'Poll Ending Soon: ' . $pollTitle, "The poll '{$pollTitle}' is ending in less than {$hoursRemaining} hours.", [
            'category' => 'Poll Activity',
            'icon' => 'clock',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_poll',
            'group_key' => 'remind_' . $pollId . '_' . $hoursRemaining . '_' . $userId
        ]);
    }

    public function notifyPollUpdated($userId, $pollId, $pollTitle) {
        $this->notify($userId, NotificationTypes::POLL_UPDATED, 'Poll Updated: ' . $pollTitle, "Important updates were made to '{$pollTitle}'.", [
            'category' => 'Poll Activity',
            'icon' => 'edit-3',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_poll',
            'group_key' => 'poll_updated_' . $pollId . '_' . $userId
        ]);
    }

    public function notifyPollEnded($userId, $pollId, $pollTitle) {
        $this->notify($userId, NotificationTypes::POLL_ENDED, 'Poll Results Published: ' . $pollTitle, "Voting has ended for '{$pollTitle}' and results are now available.", [
            'category' => 'Poll Activity',
            'icon' => 'flag',
            'entity_type' => 'poll',
            'entity_id' => $pollId,
            'action_type' => 'view_poll',
            'group_key' => 'poll_ended_' . $pollId . '_' . $userId
        ]);
    }

    public function notifySecurityAlert($userId, $action, $ip) {
        $this->notify($userId, NotificationTypes::SECURITY_ALERT, 'Security Alert', "Action: {$action} was performed from IP: {$ip}.", [
            'category' => 'Account & Security',
            'icon' => 'shield-alert',
            'entity_type' => 'security',
            'action_type' => 'view_security'
        ]);
    }
}
