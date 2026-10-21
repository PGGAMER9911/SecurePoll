<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Models\Poll;
use app\Helpers\Session;
use app\Services\PollService;

class PublicController extends Controller {
    private $pollModel;

    public function __construct() {
        try {
            $this->pollModel = new Poll();
        } catch (\Throwable $e) {
            $this->pollModel = null;
        }
    }

    /**
     * Public landing homepage.
     */
    public function home() {
        Session::init();
        
        // Fetch active public polls for display (strictly respecting Public visibility)
        $allPublicPolls = [];
        if ($this->pollModel) {
            try {
                $allPublicPolls = $this->pollModel->getAllActivePublic() ?: [];
            } catch (\Throwable $e) {
                error_log("Failed to fetch public polls: " . $e->getMessage());
                $allPublicPolls = [];
            }
        }
        
        // Filter to ensure only public and non-draft polls are shown
        $featuredPolls = array_filter($allPublicPolls, function($poll) {
            return ($poll['visibility'] === 'Public' && $poll['status'] !== 'Draft');
        });
        
        // Limit to top 6 most recent public polls
        $featuredPolls = array_slice($featuredPolls, 0, 6);

        return $this->view('public/home', [
            'pageTitle' => 'SecurePoll — Privacy-Centric Institutional Voting',
            'polls' => $featuredPolls,
            'isLoggedIn' => (bool)Session::get('user_id'),
            'userName' => Session::get('user_name') ?? ''
        ]);
    }

    /**
     * About page with project purpose and creator cards.
     */
    public function about() {
        Session::init();

        // Project creators data based strictly on repository records and verified roles
        $creators = [
            [
                'name' => 'Parth Gamit',
                'handle' => 'PGGAMER9911',
                'role' => 'Lead Backend & Security Architect',
                'badge' => 'Core Creator',
                'avatar' => 'https://github.com/PGGAMER9911.png',
                'fallback_avatar' => '/SecurePoll/public/images/logo-square.png',
                'github' => 'https://github.com/PGGAMER9911',
                'repo' => 'https://github.com/PGGAMER9911/SecurePoll',
                'email' => 'gamitparth04@gmail.com',
                'bio' => 'Original creator and backend architect of SecurePoll. Engineered the PHP MVC core, salted cryptographic voter hash architecture (voter_hash), multi-tenant organization engine, audit trails, and device trust verification systems.'
            ],
            [
                'name' => 'UI / Frontend Contributor',
                'handle' => 'SecurePoll Designer',
                'role' => 'UI/UX & Frontend Design Contributor',
                'badge' => 'Design & Systems',
                'avatar' => '/SecurePoll/public/images/avatar-designer.png',
                'fallback_avatar' => '/SecurePoll/public/images/avatar-designer.png',
                'github' => 'https://github.com/PGGAMER9911/SecurePoll',
                'repo' => 'https://github.com/PGGAMER9911/SecurePoll',
                'email' => 'contact@pgdev.in',
                'bio' => 'Collaborator responsible for SecurePoll\'s frontend design system migration to Tailwind CSS, responsive accessible interface cards, dark teal visual direction, and the client-side theme engine.'
            ]
        ];

        return $this->view('public/about', [
            'pageTitle' => 'About SecurePoll — Creators & Philosophy',
            'creators' => $creators,
            'isLoggedIn' => (bool)Session::get('user_id'),
            'userName' => Session::get('user_name') ?? ''
        ]);
    }

    /**
     * Comprehensive product documentation.
     */
    public function docs() {
        Session::init();

        return $this->view('public/docs', [
            'pageTitle' => 'Documentation — SecurePoll Voting Platform',
            'isLoggedIn' => (bool)Session::get('user_id'),
            'userName' => Session::get('user_name') ?? ''
        ]);
    }

    /**
     * Transparent Free & Community pricing information.
     */
    public function pricing() {
        Session::init();

        return $this->view('public/pricing', [
            'pageTitle' => 'Pricing & Availability — SecurePoll',
            'isLoggedIn' => (bool)Session::get('user_id'),
            'userName' => Session::get('user_name') ?? ''
        ]);
    }

    /**
     * Minimal contact page.
     */
    public function contact() {
        Session::init();

        return $this->view('public/contact', [
            'pageTitle' => 'Contact — SecurePoll',
            'isLoggedIn' => (bool)Session::get('user_id'),
            'userName' => Session::get('user_name') ?? ''
        ]);
    }
}
