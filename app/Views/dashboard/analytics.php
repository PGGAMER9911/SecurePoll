<?php
$pageTitle = 'Analytics - SecurePoll';
$pageHeader = 'Platform Analytics';
ob_start();
?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Analytics Overview</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Deep insights into your poll performance and voting trends.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl p-6 shadow-sm dark:shadow-lg flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Polls Created</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1"><?php echo number_format($totalPolls); ?></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
            <i data-lucide="bar-chart" class="w-6 h-6 text-indigo-400"></i>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl p-6 shadow-sm dark:shadow-lg flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Votes Received</p>
            <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1"><?php echo number_format($totalVotesCast); ?></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
            <i data-lucide="check-circle" class="w-6 h-6 text-emerald-400"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Poll Engagement Chart -->
    <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden p-6 relative">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Poll Engagement (Votes)</h3>
            <div class="p-2 rounded-lg bg-indigo-500/10 border border-indigo-500/20">
                <i data-lucide="trending-up" class="w-4 h-4 text-indigo-400"></i>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="engagementChart"></canvas>
        </div>
    </div>

    <!-- Future Updates Placeholder -->
    <div class="bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-sm dark:shadow-xl overflow-hidden p-6 relative flex flex-col items-center justify-center text-center h-full min-h-[350px]">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-emerald-500/5 pointer-events-none"></div>
        <div class="relative z-10 w-20 h-20 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 backdrop-blur-md flex items-center justify-center mb-6 border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-xl">
            <div class="absolute inset-0 bg-indigo-500/20 blur-xl animate-pulse"></div>
            <i data-lucide="lock" class="w-10 h-10 text-indigo-400 relative z-10"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 relative z-10">Advanced Analytics Locked</h3>
        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6 relative z-10 text-sm">
            Demographics, voting heatmaps, and deep voter insights are rolling out in Phase 4 of SecurePoll Enterprise.
        </p>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-medium relative z-10">
            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Coming Soon
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('engagementChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)'); // indigo-500
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');
    
    const chartData = {
        labels: <?php echo $pollTitles; ?>,
        datasets: [{
            label: 'Total Votes',
            data: <?php echo $pollVotes; ?>,
            backgroundColor: gradient,
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
            hoverBackgroundColor: '#818cf8',
            hoverBorderColor: '#e0e7ff'
        }]
    };

    const config = {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#f1f5f9',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' votes';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: 'Inter',
                            size: 11
                        },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: {
                            family: 'Inter',
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    };

    new Chart(ctx, config);
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
