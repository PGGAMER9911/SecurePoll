<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\Session;

class DashboardController extends Controller {
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $userId = Session::get('user_id');
        $pollModel = new \app\Models\Poll();
        $voteModel = new \app\Models\Vote();

        $polls = $pollModel->getByCreator($userId);
        $totalPolls = count($polls);
        $totalVotesCast = $voteModel->getTotalVotesForCreator($userId);
        
        return $this->view('dashboard/index', [
            'polls' => $polls,
            'totalPolls' => $totalPolls,
            'totalVotesCast' => $totalVotesCast
        ]);
    }

    public function analytics() {
        $userId = Session::get('user_id');
        $pollModel = new \app\Models\Poll();
        $voteModel = new \app\Models\Vote();

        $polls = $pollModel->getByCreator($userId);
        $totalPolls = count($polls);
        $totalVotesCast = $voteModel->getTotalVotesForCreator($userId);
        
        $pollTitles = [];
        $pollVotes = [];
        
        // Let's get up to 10 latest polls for the chart
        $chartPolls = array_slice($polls, 0, 10);
        foreach ($chartPolls as $poll) {
            $pollTitles[] = $poll['title'];
            $pollVotes[] = $voteModel->getTotalVotes($poll['id']);
        }
        
        // Reverse them so oldest of the latest 10 is on the left
        $pollTitles = array_reverse($pollTitles);
        $pollVotes = array_reverse($pollVotes);

        return $this->view('dashboard/analytics', [
            'totalPolls' => $totalPolls,
            'totalVotesCast' => $totalVotesCast,
            'pollTitles' => json_encode($pollTitles),
            'pollVotes' => json_encode($pollVotes)
        ]);
    }

    public function invitations() {
        $pollInvitationModel = new \app\Models\PollInvitation();
        $invitations = $pollInvitationModel->getByUserEmail(\app\Helpers\Session::get('user_email'));
        return $this->view('dashboard/invitations', ['invitations' => $invitations]);
    }

    public function acceptInvitation() {
        $this->requireAuthApi();
        $invitationId = $_POST['invitation_id'] ?? null;
        if (!$invitationId) return $this->json(['success' => false, 'message' => 'Missing ID.']);
        
        if (!\app\Helpers\Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $pollInvitationModel = new \app\Models\PollInvitation();
        $invitation = $pollInvitationModel->getById($invitationId);
        
        if (!$invitation || $invitation['email'] !== \app\Helpers\Session::get('user_email')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized.']);
        }
        
        $pollInvitationModel->updateStatus($invitationId, 'Accepted');
        
        $poll = (new \app\Models\Poll())->getById($invitation['poll_id']);
        if ($poll) {
            $notificationService = new \app\Services\NotificationService();
            $notificationService->notify(
                $poll['creator_id'], 
                \app\Constants\NotificationTypes::INVITATION_ACCEPTED, 
                'Invitation Accepted: ' . $poll['title'], 
                "Someone accepted your invitation to '{$poll['title']}'.", 
                ['category' => 'Invitations', 'entity_type' => 'poll', 'entity_id' => $poll['id'], 'icon' => 'user-check']
            );
        }
        
        return $this->json(['success' => true, 'message' => 'Invitation accepted.']);
    }

    public function declineInvitation() {
        $this->requireAuthApi();
        $invitationId = $_POST['invitation_id'] ?? null;
        if (!$invitationId) return $this->json(['success' => false, 'message' => 'Missing ID.']);
        
        if (!\app\Helpers\Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) { return $this->json(['success' => false, 'message' => 'CSRF token validation failed.'], 403); }
        
        $pollInvitationModel = new \app\Models\PollInvitation();
        $invitation = $pollInvitationModel->getById($invitationId);
        
        if (!$invitation || $invitation['email'] !== \app\Helpers\Session::get('user_email')) {
             return $this->json(['success' => false, 'message' => 'Unauthorized.']);
        }
        
        $pollInvitationModel->updateStatus($invitationId, 'Declined');
        
        $poll = (new \app\Models\Poll())->getById($invitation['poll_id']);
        if ($poll) {
            $notificationService = new \app\Services\NotificationService();
            $notificationService->notify(
                $poll['creator_id'], 
                \app\Constants\NotificationTypes::INVITATION_DECLINED, 
                'Invitation Declined: ' . $poll['title'], 
                "An invitation to '{$poll['title']}' was declined.", 
                ['category' => 'Invitations', 'entity_type' => 'poll', 'entity_id' => $poll['id'], 'icon' => 'user-x']
            );
        }
        
        return $this->json(['success' => true, 'message' => 'Invitation declined.']);
    }
}
