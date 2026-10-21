<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Models\User;
use app\Models\Poll;

class ProfileController extends Controller {
    private $userModel;
    private $pollModel;

    public function __construct() {
        $this->userModel = new User();
        $this->pollModel = new Poll();
    }

    public function show() {
        $userId = $_GET['id'] ?? \app\Helpers\Session::get('user_id');
        
        if (!$userId || !is_numeric($userId)) {
            $this->render404();
            return;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->render404();
            return;
        }

        // Only fetch active, public polls for this user
        $allUserPolls = $this->pollModel->getByCreator($userId);
        $publicPolls = array_filter($allUserPolls, function($p) {
            return $p['status'] !== 'Draft' && $p['visibility'] === 'Public';
        });

        $this->view('profile/show', [
            'profileUser' => $user,
            'polls' => array_values($publicPolls),
            'totalVotes' => $this->pollModel->getTotalVotesForCreator($userId)
        ]);
    }

    private function render404() {
        header("HTTP/1.0 404 Not Found");
        $this->view('errors/404', ['message' => 'User not found.']);
    }
}
