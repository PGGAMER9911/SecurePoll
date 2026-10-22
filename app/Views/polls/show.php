<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
use app\Helpers\Security;

$pageTitle = 'Vote - ' . htmlspecialchars($poll['title']);
$pageHeader = 'Cast Your Vote';
ob_start();

$options = $poll['options'] ?? [];
$totalVotes = array_sum(array_column($options, 'votes'));
?>
<style>


    .rating-container {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
    .rating-star-input {
        display: none;
    }
    .rating-star-label {
        cursor: pointer;
        padding: 1%;
        transition: transform 0.2s;
        flex: 0 1 40px;
        max-width: 44px;
        display: flex;
        justify-content: center;
    }
    .rating-star-label svg {
        width: 100%;
        height: auto;
        color: #475569; /* slate-600 */
        fill: transparent;
        transition: all 0.2s;
    }
    .rating-star-label:hover {
        transform: scale(1.1);
    }
    /* When a star is checked or hovered, color it and all subsequent (leftwards) siblings */
    .rating-star-input:checked ~ .rating-star-label svg,
    .rating-star-label:hover ~ .rating-star-label svg,
    .rating-star-label:hover svg {
        color: #f59e0b; /* amber-500 */
        fill: #f59e0b;
        filter: drop-shadow(0 0 8px rgba(245,158,11,0.5));
    }
</style>

<div class="max-w-5xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight leading-tight mb-3 break-words"><?php echo htmlspecialchars($poll['title']); ?></h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto break-words">
                <?php echo htmlspecialchars($poll['description'] ?: 'Cast your vote below.'); ?>
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 backdrop-blur-sm">
                <i data-lucide="users" class="w-4 h-4"></i> <?php echo $totalVotes; ?> Votes
            </span>
            <?php if ($poll['is_anonymous']): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 backdrop-blur-sm" title="Your identity is protected">
                    <i data-lucide="shield-check" class="w-4 h-4"></i> Anonymous
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Voting Area -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow dark:shadow-2xl p-6 sm:p-8 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent pointer-events-none"></div>
                
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2 relative">
                    <i data-lucide="check-square" class="w-5 h-5 text-indigo-400"></i> Your Choice
                </h3>

                <?php 
                $isCreator = (\app\Helpers\Session::get('user_id') == $poll['creator_id']); 
                $pollStatus = \app\Services\PollService::getLifecycleStatus($poll);
                ?>
                <?php if ($isCreator): ?>
                    <div class="p-6 text-center border-2 border-dashed border-indigo-500/30 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                        <i data-lucide="shield-alert" class="w-10 h-10 text-indigo-500 dark:text-indigo-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Creator View</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">You cannot vote on your own poll. The results and analytics are available on the right.</p>
                    </div>
                <?php elseif ($hasVoted): ?>
                    <div class="p-6 text-center border-2 border-dashed border-emerald-500/30 rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                        <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 dark:text-emerald-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Vote Recorded</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Thank you! Your vote has already been recorded for this poll. You can view the live results on the right.</p>
                    </div>
                <?php elseif ($pollStatus === 'Upcoming'): ?>
                    <div class="p-6 text-center border-2 border-dashed border-indigo-500/30 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                        <i data-lucide="clock" class="w-10 h-10 text-indigo-500 dark:text-indigo-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Poll Not Started</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">This poll has not started yet. It is scheduled to start on <?php echo date('M j, Y \a\t g:i A', strtotime($poll['start_time'])); ?>.</p>
                    </div>
                <?php elseif ($pollStatus === 'Paused'): ?>
                    <div class="p-6 text-center border-2 border-dashed border-amber-500/30 rounded-xl bg-amber-50 dark:bg-amber-500/10">
                        <i data-lucide="pause-circle" class="w-10 h-10 text-amber-500 dark:text-amber-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Poll Paused</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Voting for this poll is temporarily paused by the creator.</p>
                    </div>
                <?php elseif ($pollStatus === 'Archived'): ?>
                    <div class="p-6 text-center border-2 border-dashed border-slate-300 dark:border-slate-500/30 rounded-xl bg-slate-50 dark:bg-slate-500/10">
                        <i data-lucide="archive" class="w-10 h-10 text-slate-500 dark:text-slate-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Poll Archived</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">This poll has been archived and is no longer accepting votes.</p>
                    </div>
                <?php elseif ($pollStatus === 'Closed' || $pollStatus === 'Ended'): ?>
                    <div class="p-6 text-center border-2 border-dashed border-slate-300 dark:border-slate-500/30 rounded-xl bg-slate-50 dark:bg-slate-500/10">
                        <i data-lucide="lock" class="w-10 h-10 text-slate-500 dark:text-slate-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Poll Closed</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">This poll is closed and no longer accepting votes. <?php if ($poll['end_time']) echo 'It closed on ' . date('M j, Y \a\t g:i A', strtotime($poll['end_time'])) . '.'; ?></p>
                    </div>
                <?php elseif (!\app\Helpers\Session::get('user_id')): ?>
                    <div class="p-6 text-center border-2 border-dashed border-indigo-500/30 rounded-xl bg-indigo-50 dark:bg-indigo-500/10">
                        <i data-lucide="log-in" class="w-10 h-10 text-indigo-500 dark:text-indigo-400 mx-auto mb-3"></i>
                        <h4 class="text-slate-900 dark:text-white font-medium mb-1">Authentication Required</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">You must be logged in to participate in this poll.</p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="<?php echo $basePrefix; ?>/login" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition-all">Sign In</a>
                            <a href="<?php echo $basePrefix; ?>/register" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-all">Register</a>
                        </div>
                    </div>
                <?php else: ?>

                <div id="voteAlert" class="hidden mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md"></div>

                <form id="voteForm" class="space-y-6 relative">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                    <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>">
                    <?php if (isset($_GET['token'])): ?>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <?php endif; ?>

                    <?php if ($poll['voting_type'] === 'Rating'): ?>
                        <div class="p-6 rounded-2xl border border-slate-200 dark:border-teal-950/80 bg-slate-50/50 dark:bg-[#07181b]/50 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20 text-[11px] font-semibold mb-3">
                                <i data-lucide="star" class="w-3.5 h-3.5"></i>
                                <span>Rating Scale Ballot</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-4 font-medium">Select your rating score from 1 to 5 stars:</p>
                            
                            <!-- Hidden input to store the selected option_id -->
                            <input type="hidden" name="option_id" id="ratingHiddenInput" required>
                            
                            <!-- Rating Container -->
                            <div id="interactiveRatingContainer" class="flex justify-center items-center gap-1 cursor-pointer" data-options='<?php echo htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8'); ?>'>
                                <!-- Stars injected by JS -->
                            </div>
                            
                            <p id="ratingFeedbackText" class="text-lg font-bold text-amber-500 mt-2 h-7"></p>
                        </div>
                    <?php else: ?>
                        <!-- Explicit Voting Rule Instruction Notice -->
                        <div class="p-3 rounded-xl bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200 dark:border-teal-950/80 text-xs text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i data-lucide="<?php echo $poll['voting_type'] === 'Multiple' ? 'check-square' : 'disc'; ?>" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0"></i>
                            <span>
                                <?php if ($poll['voting_type'] === 'Multiple'): ?>
                                    <strong>Multiple Choice:</strong> Select one or more options that reflect your vote.
                                <?php else: ?>
                                    <strong>Single Choice:</strong> Select exactly one preferred option to cast your ballot.
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($options as $option): ?>
                                <label class="relative flex items-center p-4 cursor-pointer rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-[#07181b]/70 hover:bg-slate-50 dark:hover:bg-teal-950/20 transition-all custom-label group/item has-[:checked]:bg-teal-500/10 has-[:checked]:border-teal-500 dark:has-[:checked]:border-teal-500/60 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-teal-500 has-[:focus-visible]:ring-offset-2 dark:has-[:focus-visible]:ring-offset-slate-900 shadow-xs">
                                    <?php if ($poll['voting_type'] === 'Multiple'): ?>
                                        <input type="checkbox" name="options[]" value="<?php echo $option['id']; ?>" class="peer sr-only custom-input">
                                        <div class="checkbox-outer w-5 h-5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 peer-checked:bg-teal-600 peer-checked:border-teal-600 mr-4 flex items-center justify-center transition-colors shadow-xs group-hover/item:border-teal-500">
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-white opacity-0 group-has-[:checked]/item:opacity-100 transition-opacity checkbox-icon"></i>
                                        </div>
                                    <?php else: ?>
                                        <input type="radio" name="option_id" value="<?php echo $option['id']; ?>" class="peer sr-only custom-input" required>
                                        <div class="radio-outer w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 peer-checked:border-teal-600 mr-4 flex items-center justify-center transition-all shadow-xs group-hover/item:border-teal-500">
                                            <div class="radio-inner w-2.5 h-2.5 rounded-full bg-teal-600 transition-transform scale-0 group-has-[:checked]/item:scale-100"></div>
                                        </div>
                                    <?php endif; ?>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 transition-colors label-text group-has-[:checked]/item:text-teal-950 dark:group-has-[:checked]/item:text-white group-has-[:checked]/item:font-semibold">
                                        <?php echo htmlspecialchars($option['option_text']); ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" id="submitBtn" class="w-full relative bg-teal-600 hover:bg-teal-500 text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-xs hover:shadow-teal-500/20 disabled:opacity-50 disabled:cursor-not-allowed group">
                        <span id="btnText" class="text-base font-semibold">Submit Verified Ballot</span>
                        <i data-lucide="loader-2" id="spinner" class="w-5 h-5 animate-spin hidden"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Column: Results Chart -->
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow dark:shadow-2xl p-6 sm:p-8 h-full flex flex-col relative">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-emerald-400"></i> Live Results
                </h3>
                
                <?php if ($poll['result_visibility'] === 'Hidden' && \app\Helpers\Session::get('user_id') != $poll['creator_id']): ?>
                    <div class="flex-1 flex flex-col items-center justify-center py-12 text-slate-500 border-2 border-dashed border-slate-200 dark:border-slate-700/50 rounded-xl bg-slate-50 dark:bg-slate-900/50">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                            <i data-lucide="eye-off" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <p class="font-medium text-slate-700 dark:text-slate-300">Results are Hidden</p>
                        <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">The poll creator has configured results to remain private.</p>
                    </div>
                <?php elseif ($totalVotes > 0): ?>
                    <div class="flex flex-col w-full h-full gap-6">
                        <?php if ($poll['voting_type'] === 'Rating'): ?>
                            <?php 
                                $totalRatingValue = 0;
                                foreach ($options as $opt) {
                                    $totalRatingValue += floatval($opt['option_text']) * $opt['votes'];
                                }
                                $avgRating = $totalVotes > 0 ? round($totalRatingValue / $totalVotes, 1) : 0;
                                
                                // Filter and sort options for the distribution list (only showing ones with votes)
                                $distributionOptions = array_filter($options, function($opt) {
                                    return $opt['votes'] > 0;
                                });
                                usort($distributionOptions, function($a, $b) {
                                    return floatval($b['option_text']) <=> floatval($a['option_text']);
                                });
                            ?>
                            
                            <!-- Controlled chart container -->
                            <div class="relative w-full max-w-[260px] aspect-square mx-auto flex-shrink-0">
                                <canvas id="resultsChart"></canvas>
                                <!-- Center Text for Rating -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <div class="flex items-center gap-1 text-amber-500 mb-0.5">
                                        <i data-lucide="star" class="w-5 h-5 fill-amber-500 drop-shadow-md"></i>
                                    </div>
                                    <span class="text-4xl font-bold text-slate-900 dark:text-white leading-none tracking-tight"><?php echo number_format($avgRating, 1); ?></span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1.5 uppercase tracking-widest">Average</span>
                                </div>
                            </div>
                            
                            <!-- Rating Distribution -->
                            <div class="mt-6 space-y-2.5 border-t border-slate-200 dark:border-slate-700/50 pt-6">
                                <?php foreach ($distributionOptions as $opt): 
                                    $percentage = $totalVotes > 0 ? round(($opt['votes'] / $totalVotes) * 100) : 0;
                                ?>
                                <div class="flex items-center gap-3 text-sm group">
                                    <span class="w-8 font-medium text-slate-700 dark:text-slate-300 text-right"><?php echo number_format(floatval($opt['option_text']), 1); ?></span>
                                    <div class="flex-1 h-2 bg-slate-100/80 dark:bg-slate-800/80 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-amber-500 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(245,158,11,0.5)] group-hover:bg-amber-400" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="w-10 text-right text-slate-500 dark:text-slate-400 text-xs tabular-nums"><?php echo $percentage; ?>%</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="relative w-full h-full flex-1" style="min-height: 350px;">
                                <canvas id="resultsChart"></canvas>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="flex-1 flex flex-col items-center justify-center py-12 text-slate-500 border-2 border-dashed border-slate-200 dark:border-slate-700/50 rounded-xl bg-slate-50 dark:bg-slate-900/50">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                            <i data-lucide="bar-chart-3" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <p class="font-medium text-slate-700 dark:text-slate-300">No votes cast yet</p>
                        <p class="text-sm mt-1 text-slate-500 dark:text-slate-400">Be the first to share your opinion!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Star Rating Interactivity (Interactive Half-Stars)
    const ratingContainer = document.getElementById('interactiveRatingContainer');
    const hiddenInput = document.getElementById('ratingHiddenInput');
    const feedbackText = document.getElementById('ratingFeedbackText');
    
    if (ratingContainer) {
        const optionsData = JSON.parse(ratingContainer.getAttribute('data-options') || '[]');
        
        // Sort options by float value to ensure order: 0.5, 1.0, 1.5, ...
        optionsData.sort((a, b) => parseFloat(a.option_text) - parseFloat(b.option_text));
        
        // Find max value
        let maxVal = 5;
        if (optionsData.length > 0) {
            maxVal = Math.ceil(parseFloat(optionsData[optionsData.length - 1].option_text));
        }
        
        // Render empty stars
        ratingContainer.innerHTML = '';
        ratingContainer.className = 'flex justify-center items-center gap-1 sm:gap-1.5 w-full max-w-full mx-auto cursor-pointer flex-wrap';
        const sizeClasses = maxVal > 5 ? 'w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8' : 'w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14';
        
        for (let i = 1; i <= maxVal; i++) {
            const starWrapper = document.createElement('div');
            starWrapper.className = `relative flex-shrink-0 transition-transform duration-200 hover:scale-110 ${sizeClasses}`;
            
            // Empty star (background) and Filled star container
            starWrapper.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-0 left-0 w-full h-full text-slate-700" fill="transparent" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <div class="star-fill absolute top-0 left-0 h-full overflow-hidden w-0 transition-all duration-75">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-0 left-0 ${sizeClasses}" fill="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
            `;
            ratingContainer.appendChild(starWrapper);
        }
        
        let currentHoverValue = 0;
        let selectedValue = 0;
        const stars = ratingContainer.querySelectorAll('.relative');
        
        function updateVisuals(val) {
            // Determine dynamic smooth color progression based on rating percentage
            let percentage = maxVal > 0 ? (val / maxVal) : 0;
            
            // Continuous HSL Interpolation
            // Hue: 0 (Red) to 50 (Gold)
            const hue = percentage * 50;
            const saturation = 90 + (10 * percentage); // 90% to 100%
            const lightness = 48 + (6 * percentage); // 48% (Deep Red) to 54% (Premium Gold)
            
            const color = `hsl(${hue}, ${saturation}%, ${lightness}%)`;
            
            // Glow intensity smoothly increases with rating
            const glowOpacity = 0.3 + (0.5 * percentage); // 0.3 to 0.8
            const glowRadius = 4 + (8 * percentage); // 4px to 12px
            const dropShadow = `drop-shadow(0 0 ${glowRadius}px hsla(${hue}, ${saturation}%, ${lightness}%, ${glowOpacity}))`;
            
            stars.forEach((star, index) => {
                const fill = star.querySelector('.star-fill');
                const svg = fill.querySelector('svg');
                const starNum = index + 1;
                
                // Set width for filling (full, half, or empty)
                if (val >= starNum) {
                    fill.style.width = '100%';
                } else if (val >= starNum - 0.5) {
                    fill.style.width = '50%';
                } else {
                    fill.style.width = '0%';
                }
                
                // Apply dynamic smooth color and glow
                if (val > 0) {
                    fill.style.color = color;
                    svg.style.filter = dropShadow;
                } else {
                    fill.style.color = 'transparent';
                    svg.style.filter = 'none';
                }
            });
            
            if (val > 0) {
                feedbackText.textContent = val.toFixed(1) + ' Stars';
                feedbackText.style.color = color;
                feedbackText.style.textShadow = `0 0 10px hsla(${hue}, ${saturation}%, ${lightness}%, 0.5)`;
            } else {
                feedbackText.textContent = '';
                feedbackText.style.textShadow = 'none';
            }
        }
        
        ratingContainer.addEventListener('mousemove', (e) => {
            const rect = ratingContainer.getBoundingClientRect();
            // Calculate which star we are over
            let hoveredStar = null;
            let starIndex = -1;
            
            stars.forEach((star, idx) => {
                const sRect = star.getBoundingClientRect();
                if (e.clientX >= sRect.left && e.clientX <= sRect.right) {
                    hoveredStar = star;
                    starIndex = idx;
                }
            });
            
            if (hoveredStar) {
                const sRect = hoveredStar.getBoundingClientRect();
                const isHalf = (e.clientX - sRect.left) < (sRect.width / 2);
                currentHoverValue = isHalf ? (starIndex + 0.5) : (starIndex + 1.0);
                updateVisuals(currentHoverValue);
            }
        });
        
        ratingContainer.addEventListener('mouseleave', () => {
            currentHoverValue = 0;
            updateVisuals(selectedValue);
        });
        
        ratingContainer.addEventListener('click', () => {
            if (currentHoverValue > 0) {
                selectedValue = currentHoverValue;
                // Find matching option_id
                const targetText = selectedValue.toFixed(1);
                const match = optionsData.find(o => parseFloat(o.option_text).toFixed(1) === targetText);
                if (match) {
                    hiddenInput.value = match.id;
                    feedbackText.classList.add('animate-fade-in-up');
                    setTimeout(() => feedbackText.classList.remove('animate-fade-in-up'), 300);
                }
            }
        });
    }

    // Form Submission
    const form = document.getElementById('voteForm');
    const alertBox = document.getElementById('voteAlert');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alertBox.classList.add('hidden');
            
            // Multiple choice validation
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            if (checkboxes.length > 0) {
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                if (checked.length === 0) {
                    alertBox.classList.remove('hidden');
                    alertBox.className = 'mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border bg-red-500/10 text-red-400 border-red-500/20';
                    alertBox.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i><div>Please select at least one option.</div>';
                    lucide.createIcons();
                    return;
                }
            }

            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-busy', 'true');
            const btnText = document.getElementById('btnText');
            if (btnText) btnText.textContent = 'Submitting Vote...';
            submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.remove('hidden'));
            
            const formData = new FormData(form);
            
            fetch((window.SecurePollBase || '') + '/polls/vote', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                alertBox.classList.remove('hidden');
                if (data.success) {
                    alertBox.className = 'mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                    alertBox.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i><div>' + (data.message || 'Vote recorded successfully!') + '</div>';
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alertBox.className = 'mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border bg-red-500/10 text-red-400 border-red-500/20';
                    alertBox.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i><div>' + (data.message || 'An error occurred.') + '</div>';
                    submitBtn.disabled = false;
                    submitBtn.removeAttribute('aria-busy');
                    if (btnText) btnText.textContent = 'Submit Vote';
                    submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.add('hidden'));
                }
                lucide.createIcons();
            })
            .catch(err => {
                alertBox.classList.remove('hidden');
                alertBox.className = 'mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border bg-red-500/10 text-red-400 border-red-500/20';
                alertBox.innerHTML = '<i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i><div>A network error occurred. Please try again.</div>';
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-busy');
                if (btnText) btnText.textContent = 'Submit Vote';
                submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.add('hidden'));
                lucide.createIcons();
            });
        });
    }

    // Chart.js rendering
    <?php if ($totalVotes > 0 && !($poll['result_visibility'] === 'Hidden' && \app\Helpers\Session::get('user_id') != $poll['creator_id'])): ?>
    const ctx = document.getElementById('resultsChart');
    if (ctx) {
        const rawColors = ['#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6', '#f43f5e'];
        const isRating = <?php echo ($poll['voting_type'] === 'Rating') ? 'true' : 'false'; ?>;
        
        let bgColors, borderColors;
        
        if (isRating) {
            // Use an amber/orange gradient for ratings
            const amberColors = ['#fffbeb', '#fef3c7', '#fde68a', '#fcd34d', '#fbbf24', '#f59e0b', '#d97706', '#b45309', '#92400e', '#78350f'];
            // Labels are numbers 0.5, 1.0, etc. We map them.
            bgColors = <?php echo json_encode(array_column($options, 'option_text')); ?>.map((lbl, idx) => {
                let val = Math.round(parseFloat(lbl)); // 0-10
                if (val < 1) val = 1; if(val > 10) val = 10;
                return amberColors[val-1] + 'CC';
            });
            borderColors = <?php echo json_encode(array_column($options, 'option_text')); ?>.map((lbl, idx) => {
                let val = Math.round(parseFloat(lbl));
                if (val < 1) val = 1; if(val > 10) val = 10;
                return amberColors[val-1];
            });
        } else {
            bgColors = rawColors.map(c => c + 'CC');
            borderColors = rawColors;
        }
        
        if (window.myPollChart) {
            window.myPollChart.destroy();
        }
        
        window.myPollChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($options, 'option_text')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($options, 'votes')); ?>,
                    backgroundColor: bgColors,
                    borderColor: '#0f172a',
                    borderWidth: 2,
                    hoverOffset: 8,
                    hoverBorderColor: borderColors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: isRating ? '78%' : '65%', // Thinner doughnut for rating to make room for center text
                layout: {
                    padding: isRating ? 10 : 20
                },
                plugins: {
                    legend: {
                        display: !isRating,
                        position: 'right',
                        labels: {
                            color: '#94a3b8',
                            font: { family: "'Inter', sans-serif", size: 13, weight: 500 },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) label += isRating ? ' Stars: ' : ': ';
                                if (context.parsed !== null) {
                                    label += context.parsed + ' votes';
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = Math.round((context.parsed / total) * 100) + '%';
                                    label += ' (' + percentage + ')';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>

<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>