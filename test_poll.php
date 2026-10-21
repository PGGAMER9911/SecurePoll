<?php
// Autoloader function for classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load Environment Variables
\app\Helpers\Env::load(__DIR__ . '/.env');

// Init Session
session_start();

use app\Services\PollService;

echo "Starting Poll Engine Test...\n";

$pollService = new PollService();

// Mock User ID (we know user ID 1 exists from previous test)
$userId = 1;

// 1. Create a Poll
$pollData = [
    'title' => 'Favorite Programming Language?',
    'description' => 'A test poll to see what our users prefer.',
    'visibility' => 'Public',
    'is_anonymous' => 1
];
$options = ['PHP', 'JavaScript', 'Python', 'Go'];

echo "Creating poll...\n";
$createResult = $pollService->createPoll($userId, $pollData, $options);

if ($createResult['success']) {
    echo "SUCCESS: Poll created with ID: " . $createResult['poll_id'] . "\n";
    $pollId = $createResult['poll_id'];
} else {
    die("FAILED to create poll: " . $createResult['message'] . "\n");
}

// 2. Fetch Poll Details to get Option IDs
$pollDetails = $pollService->getPollWithDetails($pollId);
if (!$pollDetails || empty($pollDetails['options'])) {
    die("FAILED to fetch poll details or options.\n");
}
$firstOptionId = $pollDetails['options'][0]['id'];
$secondOptionId = $pollDetails['options'][1]['id'];

// 3. Cast a Vote
echo "Casting vote for option ID: $firstOptionId ...\n";
$voteResult1 = $pollService->castVote($pollId, $firstOptionId, $userId, 'test@example.com', '127.0.0.1');

if ($voteResult1['success']) {
    echo "SUCCESS: Vote cast!\n";
} else {
    echo "FAILED: " . $voteResult1['message'] . "\n";
}

// 4. Test Duplicate Vote (should fail)
echo "Attempting duplicate vote ...\n";
$voteResult2 = $pollService->castVote($pollId, $secondOptionId, $userId, 'test@example.com', '127.0.0.1');

if (!$voteResult2['success']) {
    echo "SUCCESS (Expected Failure): " . $voteResult2['message'] . "\n";
} else {
    echo "FAILED: System allowed a duplicate vote!\n";
}

// 5. Fetch Results
echo "Fetching results...\n";
$results = $pollService->getResults($pollId);
print_r($results);

echo "\nDone.\n";
