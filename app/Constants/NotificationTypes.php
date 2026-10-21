<?php
namespace app\Constants;

class NotificationTypes {
    // Poll Activity
    public const POLL_CREATED = 'poll_created';
    public const POLL_STARTED = 'poll_started';
    public const POLL_ENDING_SOON = 'poll_ending_soon';
    public const POLL_ENDED = 'poll_ended';
    public const VOTE_RECEIVED = 'vote_received';
    public const MAX_VOTERS_REACHED = 'max_voters_reached';
    public const POLL_UPDATED = 'poll_updated';
    public const POLL_DELETED = 'poll_deleted';

    // Voting
    public const VOTE_CAST = 'vote_cast';
    public const VOTE_CHANGED = 'vote_changed';
    public const VOTING_AVAILABLE = 'voting_available';
    public const VOTING_CLOSED = 'voting_closed';

    // Invitations
    public const NEW_INVITATION = 'new_invitation';
    public const INVITATION_ACCEPTED = 'invitation_accepted';
    public const INVITATION_DECLINED = 'invitation_declined';
    public const INVITATION_REVOKED = 'invitation_revoked';
    public const INVITATION_EXPIRED = 'invitation_expired';

    // Account & Security
    public const WELCOME = 'welcome';
    public const EMAIL_VERIFICATION = 'email_verification';
    public const PASSWORD_CHANGED = 'password_changed';
    public const PASSWORD_RESET = 'password_reset';
    public const SECURITY_ALERT = 'security_alert';
    public const LOGIN_NEW_DEVICE = 'login_new_device';

    // Analytics & Activity
    public const POLL_10_VOTES = 'poll_10_votes';
    public const POLL_50_VOTES = 'poll_50_votes';
    public const POLL_100_VOTES = 'poll_100_votes';
    public const POLL_SIGNIFICANT_ACTIVITY = 'poll_significant_activity';
    public const POLL_ANALYTICS_AVAILABLE = 'poll_analytics_available';
    public const WEEKLY_ACTIVITY = 'weekly_activity';

    public static function getCategories() {
        return [
            'Poll Activity' => [
                self::POLL_CREATED, self::POLL_STARTED, self::POLL_ENDING_SOON, self::POLL_ENDED,
                self::VOTE_RECEIVED, self::MAX_VOTERS_REACHED, self::POLL_UPDATED, self::POLL_DELETED
            ],
            'Voting' => [
                self::VOTE_CAST, self::VOTE_CHANGED, self::VOTING_AVAILABLE, self::VOTING_CLOSED
            ],
            'Invitations' => [
                self::NEW_INVITATION, self::INVITATION_ACCEPTED, self::INVITATION_DECLINED,
                self::INVITATION_REVOKED, self::INVITATION_EXPIRED
            ],
            'Account & Security' => [
                self::WELCOME, self::EMAIL_VERIFICATION, self::PASSWORD_CHANGED,
                self::PASSWORD_RESET, self::SECURITY_ALERT, self::LOGIN_NEW_DEVICE
            ],
            'Analytics & Activity' => [
                self::POLL_10_VOTES, self::POLL_50_VOTES, self::POLL_100_VOTES,
                self::POLL_SIGNIFICANT_ACTIVITY, self::POLL_ANALYTICS_AVAILABLE, self::WEEKLY_ACTIVITY
            ]
        ];
    }
}
