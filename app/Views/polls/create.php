<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
use app\Helpers\Security;

$pageTitle = 'Create Poll - SecurePoll';
$pageHeader = 'Create New Poll';
ob_start();
?>
<link rel="stylesheet" id="flatpickr-theme" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Customize Flatpickr to match SecurePoll theme */
    html.dark .flatpickr-calendar {
        background: #1e293b; /* slate-800 */
        border: 1px solid #334155; /* slate-700 */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    }
    .flatpickr-day.selected {
        background: #6366f1 !important; /* indigo-500 */
        border-color: #6366f1 !important;
    }
</style>
<script>
    function updateFlatpickrTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.getElementById('flatpickr-theme').href = "https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css";
        } else {
            document.getElementById('flatpickr-theme').href = "https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css";
        }
    }
    document.addEventListener('DOMContentLoaded', updateFlatpickrTheme);
    document.addEventListener('themeChanged', updateFlatpickrTheme);
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Create New Poll</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure your poll settings and options to gather insights.</p>
    </div>

    <div class="relative bg-white dark:bg-slate-900/60 backdrop-blur-xl border border-slate-200 dark:border-white/5 rounded-xl shadow-md dark:shadow-2xl overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-transparent to-blue-500/5 pointer-events-none"></div>
        
        <div class="relative p-6 sm:p-10">
            <form id="createPollForm" class="space-y-10">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCsrfToken(); ?>">
                
                <div id="alertBox" class="hidden p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md"></div>
                
                <!-- Section 1: Basic Info -->
                <div class="space-y-6 relative">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5">
                        <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20">
                            <i data-lucide="file-text" class="w-5 h-5 text-indigo-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white tracking-tight">1. Basic Information</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Poll Title <span class="text-red-400">*</span></label>
                        <input type="text" name="title" required placeholder="e.g., What is your favorite programming language?" 
                            class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3.5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner text-lg">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Description (Optional)</label>
                        <textarea name="description" rows="3" placeholder="Provide context or instructions for your voters..." 
                            class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3.5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner resize-none"></textarea>
                    </div>
                </div>

                <!-- Section 2: Voting Type -->
                <div class="space-y-6 relative">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-white/5">
                        <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20">
                            <i data-lucide="layout-grid" class="w-5 h-5 text-emerald-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white tracking-tight">2. Voting Type</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex flex-col p-4 cursor-pointer rounded-xl border-2 border-teal-600 dark:border-teal-500 bg-teal-50/70 dark:bg-teal-950/40 text-center group type-option transition-all shadow-xs" data-type="Custom">
                            <input type="radio" name="voting_type" value="Custom" class="sr-only" checked>
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i data-lucide="list-checks" class="w-4 h-4 text-teal-600 dark:text-teal-400 type-icon"></i>
                                <span class="text-sm font-bold text-slate-900 dark:text-white type-title">Custom Options</span>
                            </div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">Single or multi-choice candidates</span>
                        </label>
                        <label class="relative flex flex-col p-4 cursor-pointer rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-slate-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-center group type-option transition-all shadow-xs" data-type="Yes/No">
                            <input type="radio" name="voting_type" value="Yes/No" class="sr-only">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i data-lucide="thumbs-up" class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-teal-500 type-icon"></i>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 type-title">Yes / No</span>
                            </div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">Parliamentary motions & binary votes</span>
                        </label>
                        <label class="relative flex flex-col p-4 cursor-pointer rounded-xl border border-slate-200 dark:border-teal-950/80 bg-white dark:bg-slate-900/50 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-center group type-option transition-all shadow-xs" data-type="Rating">
                            <input type="radio" name="voting_type" value="Rating" class="sr-only">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i data-lucide="star" class="w-4 h-4 text-slate-500 dark:text-slate-400 group-hover:text-amber-500 type-icon"></i>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 type-title">Rating Scale</span>
                            </div>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">Evaluations from 1 to 5 stars</span>
                        </label>
                    </div>
                </div>
                
                <!-- Section 3: Voting Configuration -->
                <div class="space-y-6 relative bg-slate-50 dark:bg-slate-800/20 p-6 rounded-xl border border-slate-700/30">
                    <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">3. Configuration</h3>
                    
                    <!-- Custom Options Config -->
                    <div id="config-Custom" class="config-section">
                        <div id="optionsContainer" class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 option-row group/opt">
                                <div class="flex-1">
                                    <input type="text" name="options[]" placeholder="Option 1" 
                                        class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                </div>
                                <button type="button" class="p-3 text-slate-500 rounded-xl transition-all remove-btn opacity-50 cursor-not-allowed" disabled>
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-3 option-row group/opt">
                                <div class="flex-1">
                                    <input type="text" name="options[]" placeholder="Option 2" 
                                        class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                </div>
                                <button type="button" class="p-3 text-slate-500 rounded-xl transition-all remove-btn opacity-50 cursor-not-allowed" disabled>
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="addOptionBtn" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white bg-white dark:bg-slate-800/50 hover:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl transition-all shadow-sm mb-6">
                            <i data-lucide="plus" class="w-4 h-4"></i> Add Another Option
                        </button>
                        
                        <div class="pt-6 border-t border-slate-700/50">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Choice Mode</label>
                            <!-- True native radio buttons styled with accent-color -->
                            <div class="flex gap-6">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="choice_mode" value="Single" checked class="w-5 h-5 accent-indigo-500 cursor-pointer">
                                    <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">Single Choice</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="choice_mode" value="Multiple" class="w-5 h-5 accent-indigo-500 cursor-pointer">
                                    <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">Multiple Choice</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Yes/No Config -->
                    <div id="config-Yes/No" class="config-section hidden space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <!-- Dummy inputs just for visual representation, no name attribute so they don't submit -->
                                <input type="text" value="Yes" disabled class="w-full bg-white dark:bg-slate-900/50 border border-slate-700/30 rounded-xl px-4 py-3 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input type="text" value="No" disabled class="w-full bg-white dark:bg-slate-900/50 border border-slate-700/30 rounded-xl px-4 py-3 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                            </div>
                        </div>
                        
                        <div class="pt-4 mt-2 border-t border-slate-700/50">
                            <label class="flex items-center gap-3 cursor-pointer group mb-4">
                                <input type="checkbox" name="allow_third_option" id="allowThirdOption" class="w-5 h-5 accent-indigo-500 cursor-pointer rounded">
                                <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">Allow a third option</span>
                            </label>
                            
                            <div id="thirdOptionContainer" class="hidden flex items-center gap-3">
                                <div class="flex-1">
                                    <input type="text" name="third_option_label" value="Neither / Not sure" placeholder="Enter option label" disabled
                                        class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rating Config -->
                    <div id="config-Rating" class="config-section hidden space-y-6">
                        <div class="space-y-2 max-w-xs relative">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Rating Scale</label>
                            
                            <!-- Custom Dropdown for Rating Scale -->
                            <div class="custom-select-wrapper relative">
                                <input type="hidden" name="rating_scale" id="ratingScaleInput" value="5" disabled>
                                <button type="button" class="custom-select-button w-full flex items-center justify-between bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                    <span class="select-text">5 Stars</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 dark:text-slate-400 transition-transform duration-200"></i>
                                </button>
                                <div class="custom-select-menu absolute z-50 w-full mt-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl opacity-0 invisible pointer-events-none translate-y-[-10px] transition-all duration-200">
                                    <ul class="py-1">
                                        <li>
                                            <button type="button" data-value="5" class="custom-select-option w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:bg-indigo-500/10 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center justify-between bg-indigo-500/5 text-indigo-400">
                                                5 Stars
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-value="10" class="custom-select-option w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:bg-indigo-500/10 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center justify-between">
                                                10 Stars
                                                <i data-lucide="check" class="w-4 h-4 hidden"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2">
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Preview</label>
                            <div class="flex gap-1" id="starPreviewContainer">
                                <!-- Generated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section 4: Settings & Security -->
                <div class="space-y-6 relative">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5">
                        <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20">
                            <i data-lucide="settings-2" class="w-5 h-5 text-amber-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white tracking-tight">4. Settings & Security</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Visibility</label>
                            <div class="relative group">
                                <!-- Custom Dropdown for Visibility -->
                                <div class="custom-select-wrapper relative">
                                    <input type="hidden" name="visibility" id="visibilityInput" value="Public">
                                    <button type="button" class="custom-select-button w-full flex items-center justify-between bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3.5 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                                        <span class="select-text">Public (Anyone can view)</span>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500 dark:text-slate-400 transition-transform duration-200"></i>
                                    </button>
                                    <div class="custom-select-menu absolute z-50 w-full mt-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl opacity-0 invisible pointer-events-none translate-y-[-10px] transition-all duration-200">
                                        <ul class="py-1">
                                            <li><button type="button" data-value="Public" class="custom-select-option w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:bg-indigo-500/10 transition-colors flex items-center justify-between bg-indigo-500/5 text-indigo-400">Public (Anyone can view)<i data-lucide="check" class="w-4 h-4"></i></button></li>
                                            <li><button type="button" data-value="Private" class="custom-select-option w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:bg-indigo-500/10 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center justify-between">Private (Invitation only)<i data-lucide="check" class="w-4 h-4 hidden"></i></button></li>
                                            <li><button type="button" data-value="Unlisted" class="custom-select-option w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:bg-indigo-500/10 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center justify-between">Unlisted (Link only)<i data-lucide="check" class="w-4 h-4 hidden"></i></button></li>
                                        </ul>
                                    </div>
                                </div>
                                <p id="visibilityHelper" class="text-xs text-slate-500 mt-2">Appears in public listings.</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Max Voters (Optional)</label>
                            <input type="number" name="max_voters" min="2" step="1" placeholder="Leave empty for unlimited" class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3.5 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Start Time (Optional)</label>
                            <div class="relative">
                                <input type="text" id="startTimeInput" name="start_time" placeholder="Select start time" class="w-full !bg-white dark:!bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl py-3.5 pl-11 pr-4 !text-slate-900 dark:!text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner disabled:opacity-50">
                                <i data-lucide="calendar" class="w-5 h-5 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">End Time (Optional)</label>
                            <div class="relative">
                                <input type="text" id="endTimeInput" name="end_time" placeholder="Select end time" class="w-full !bg-white dark:!bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl py-3.5 pl-11 pr-4 !text-slate-900 dark:!text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner disabled:opacity-50">
                                <i data-lucide="calendar" class="w-5 h-5 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-700/50">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="is_anonymous" id="isAnonymous" checked class="sr-only peer">
                                <div class="w-12 h-6 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500 peer-checked:border-indigo-500 shadow-inner"></div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-600 dark:group-hover:text-white transition-colors block">Anonymous Voting (Privacy Enabled)</span>
                                <span class="text-xs text-slate-500 mt-0.5 block">When enabled, voter identities (even logged-in users) will be completely hidden.</span>
                            </div>
                        </label>
                    </div>
                    
                    <div id="privateSettings" class="hidden bg-indigo-500/5 border border-indigo-100 dark:border-indigo-500/20 rounded-xl p-6 mt-4 shadow-inner">
                        <div class="flex items-start gap-3 mb-4 text-indigo-400">
                            <i data-lucide="shield-alert" class="w-5 h-5 shrink-0"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-white">Private Access Control</h4>
                                <p class="text-xs text-indigo-300 mt-1">Specify who can access this poll.</p>
                            </div>
                        </div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Eligible Voters (Emails)</label>
                        <textarea name="eligible_voters" rows="3" placeholder="user1@example.com, user2@example.com" 
                            class="w-full bg-white dark:bg-slate-950/50 border border-indigo-500/30 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all resize-none shadow-inner"></textarea>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-1.5"><i data-lucide="info" class="w-3.5 h-3.5"></i> Separate multiple emails with commas.</p>
                    </div>
                </div>
                
                <div class="pt-8 border-t border-slate-200 dark:border-teal-950/80 flex justify-end">
                    <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-teal-600 hover:bg-teal-500 transition-colors shadow-xs flex items-center justify-center gap-2">
                        <span id="btnText">Launch Poll</span>
                        <i data-lucide="loader-2" id="spinner" class="w-4 h-4 animate-spin hidden"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Custom Dropdown Logic
    function setupCustomDropdowns() {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const btn = wrapper.querySelector('.custom-select-button');
            const menu = wrapper.querySelector('.custom-select-menu');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const options = wrapper.querySelectorAll('.custom-select-option');
            const selectText = btn.querySelector('.select-text');
            
            const syncState = () => {
                const currentValue = hiddenInput.value;
                options.forEach(o => {
                    const isSelected = o.getAttribute('data-value') === currentValue;
                    const checkIcon = o.querySelector('svg, i');
                    
                    if (isSelected) {
                        o.classList.add('bg-indigo-500/5', 'text-indigo-400');
                        o.classList.remove('text-slate-700', 'dark:text-slate-300');
                        if (checkIcon) checkIcon.classList.remove('hidden');
                        
                        const clone = o.cloneNode(true);
                        const cIcon = clone.querySelector('svg, i');
                        if (cIcon) cIcon.remove();
                        selectText.textContent = clone.textContent.trim();
                    } else {
                        o.classList.remove('bg-indigo-500/5', 'text-indigo-400');
                        o.classList.add('text-slate-700', 'dark:text-slate-300');
                        if (checkIcon) checkIcon.classList.add('hidden');
                    }
                });
            };

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = !menu.classList.contains('invisible');
                
                // close all others
                document.querySelectorAll('.custom-select-menu').forEach(m => {
                    m.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                    const otherIcon = m.parentElement.querySelector('.custom-select-button svg, .custom-select-button i');
                    if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
                });
                
                if (!isOpen) {
                    syncState();
                    menu.classList.remove('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                    const myIcon = btn.querySelector('svg, i');
                    if (myIcon) myIcon.style.transform = 'rotate(180deg)';
                }
            });

            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    hiddenInput.value = opt.getAttribute('data-value');
                    syncState();
                    hiddenInput.dispatchEvent(new Event('change'));
                    menu.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                    const myIcon = btn.querySelector('svg, i');
                    if (myIcon) myIcon.style.transform = 'rotate(0deg)';
                });
            });
            
            syncState();
        });
        
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-select-menu').forEach(m => {
                m.classList.add('invisible', 'opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
                const otherIcon = m.parentElement.querySelector('.custom-select-button svg, .custom-select-button i');
                if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
            });
        });
    }
    setupCustomDropdowns();

    // 1. Voting Type Toggle (Fixed to use change event on radios to prevent bubbling issues)
    const typeRadios = document.querySelectorAll('input[name="voting_type"]');
    const configSections = document.querySelectorAll('.config-section');
    
        function updateVotingTypeUI(selectedType) {
        // Update styling of type boxes
        document.querySelectorAll('.type-option').forEach(opt => {
            const radio = opt.querySelector('input');
            const icon = opt.querySelector('.type-icon');
            const title = opt.querySelector('.type-title');
            
            if (radio.value === selectedType) {
                // OPTION Selected
                opt.classList.remove('border-slate-200', 'dark:border-teal-950/80', 'bg-white', 'dark:bg-slate-900/50', 'hover:bg-slate-50', 'dark:hover:bg-slate-800/60');
                opt.classList.add('border-2', 'border-teal-600', 'dark:border-teal-500', 'bg-teal-50/70', 'dark:bg-teal-950/40');
                
                // ICON Selected
                icon.classList.remove('text-slate-500', 'dark:text-slate-400');
                icon.classList.add('text-teal-600', 'dark:text-teal-400');
                
                // TITLE Selected
                title.classList.remove('text-slate-700', 'dark:text-slate-300', 'group-hover:text-teal-600', 'dark:group-hover:text-teal-400');
                title.classList.add('text-teal-950', 'dark:text-white', 'font-bold');
            } else {
                // OPTION Unselected
                opt.classList.remove('border-2', 'border-teal-600', 'dark:border-teal-500', 'bg-teal-50/70', 'dark:bg-teal-950/40');
                opt.classList.add('border', 'border-slate-200', 'dark:border-teal-950/80', 'bg-white', 'dark:bg-slate-900/50', 'hover:bg-slate-50', 'dark:hover:bg-slate-800/60');
                
                // ICON Unselected
                icon.classList.remove('text-teal-600', 'dark:text-teal-400');
                icon.classList.add('text-slate-500', 'dark:text-slate-400');
                
                // TITLE Unselected
                title.classList.remove('text-teal-950', 'dark:text-white', 'font-bold');
                title.classList.add('text-slate-700', 'dark:text-slate-300', 'group-hover:text-teal-600', 'dark:group-hover:text-teal-400');
            }
        });
        
        // Show correct config section and disable hidden inputs so they don't submit
        configSections.forEach(sec => {
            const inputs = sec.querySelectorAll('input[name], select[name], textarea[name]');
            if (sec.id === `config-${selectedType}`) {
                sec.classList.remove('hidden');
                sec.classList.add('animate-fade-in-up');
                inputs.forEach(i => i.disabled = false);
            } else {
                sec.classList.add('hidden');
                sec.classList.remove('animate-fade-in-up');
                inputs.forEach(i => i.disabled = true);
            }
        });
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.checked) updateVotingTypeUI(e.target.value);
        });
    });
    // Initialize
    updateVotingTypeUI(document.querySelector('input[name="voting_type"]:checked').value);

    // 2. Custom Options Logic
    const optionsContainer = document.getElementById('optionsContainer');
    const addOptionBtn = document.getElementById('addOptionBtn');
    let optionCount = 2;
    
    addOptionBtn.addEventListener('click', () => {
        optionCount++;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 option-row animate-fade-in-up group/opt';
        row.innerHTML = `
            <div class="flex-1">
                <input type="text" name="options[]" placeholder="Option ${optionCount}" 
                    class="w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-4 py-3 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all shadow-inner">
            </div>
            <button type="button" class="p-3 text-slate-500 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 rounded-xl transition-all remove-btn">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
        `;
        optionsContainer.appendChild(row);
        lucide.createIcons();
        updateRemoveButtons();
    });
    
    optionsContainer.addEventListener('click', (e) => {
        const removeBtn = e.target.closest('.remove-btn');
        if (removeBtn && !removeBtn.disabled) {
            removeBtn.closest('.option-row').remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const rows = optionsContainer.querySelectorAll('.option-row');
        const btns = optionsContainer.querySelectorAll('.remove-btn');
        if (rows.length <= 2) {
            btns.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('hover:text-red-400', 'hover:bg-red-500/10', 'hover:border-red-500/20');
            });
        } else {
            btns.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.classList.add('hover:text-red-400', 'hover:bg-red-500/10', 'hover:border-red-500/20');
            });
        }
    }

    // 3. Yes/No Third Option Logic
    const allowThirdOption = document.getElementById('allowThirdOption');
    const thirdOptionContainer = document.getElementById('thirdOptionContainer');
    const thirdOptionInput = thirdOptionContainer.querySelector('input');
    allowThirdOption.addEventListener('change', (e) => {
        if (e.target.checked) {
            thirdOptionContainer.classList.remove('hidden');
            thirdOptionInput.disabled = false;
        } else {
            thirdOptionContainer.classList.add('hidden');
            thirdOptionInput.disabled = true;
        }
    });

    // 4. Rating Preview Logic
    const ratingScaleInput = document.getElementById('ratingScaleInput');
    const starPreviewContainer = document.getElementById('starPreviewContainer');
    
    function renderStars(scale) {
        starPreviewContainer.innerHTML = '';
        for(let i=0; i<scale; i++) {
            const svg = document.createElement('div');
            svg.innerHTML = `<i data-lucide="star" class="w-6 h-6 text-amber-400 fill-amber-400/20"></i>`;
            starPreviewContainer.appendChild(svg.firstElementChild);
        }
        lucide.createIcons();
    }
    ratingScaleInput.addEventListener('change', (e) => renderStars(e.target.value));
    renderStars(5);

    // 5. Visibility Logic
    const visibilityInput = document.getElementById('visibilityInput');
    const privateSettings = document.getElementById('privateSettings');
    const visibilityHelper = document.getElementById('visibilityHelper');
    
    visibilityInput.addEventListener('change', (e) => {
        if (e.target.value === 'Private') {
            privateSettings.classList.remove('hidden');
            privateSettings.classList.add('animate-fade-in-up');
            visibilityHelper.textContent = "Only invited users can view.";
        } else {
            privateSettings.classList.add('hidden');
            privateSettings.classList.remove('animate-fade-in-up');
            if (e.target.value === 'Public') visibilityHelper.textContent = "Appears in public listings.";
            if (e.target.value === 'Unlisted') visibilityHelper.textContent = "Accessible via direct link only. Hidden from listings.";
        }
    });

    // 6. Max Voters UX Restriction
    const maxVotersInput = document.querySelector('input[name="max_voters"]');
    if (maxVotersInput) {
        maxVotersInput.addEventListener('keydown', function(e) {
            // Block invalid characters completely
            if (['e', 'E', '+', '-', '.'].includes(e.key)) {
                e.preventDefault();
                return;
            }
            // Prevent 0 as the first character
            if (e.key === '0' && this.value === '') {
                e.preventDefault();
                return;
            }
        });

        maxVotersInput.addEventListener('paste', function(e) {
            let pasteData = (e.clipboardData || window.clipboardData).getData('text');
            
            // Reject if contains any non-digit
            if (/[^0-9]/.test(pasteData)) {
                e.preventDefault();
                return;
            }
            
            // If field is empty and pasted number is < 2, block it
            if (this.value === '' && parseInt(pasteData, 10) < 2) {
                e.preventDefault();
                return;
            }
        });

        maxVotersInput.addEventListener('input', function(e) {
            if (this.value !== '') {
                let val = parseInt(this.value, 10);
                if (val <= 0 || isNaN(val)) {
                    this.value = '';
                }
            }
        });

        maxVotersInput.addEventListener('blur', function(e) {
            // 1 is invalid on its own, so clear it when leaving
            if (this.value === '1') {
                this.value = '';
            }
        });
    }

    // 7. Date/Time Constraints (Using Flatpickr)
    const startTimeInput = document.getElementById('startTimeInput');
    const endTimeInput = document.getElementById('endTimeInput');
    
    if (startTimeInput && endTimeInput) {
        const fpEnd = flatpickr(endTimeInput, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            theme: "dark",
            onChange: function(selectedDates, dateStr, instance) {
                validateEndTime();
            }
        });

        const fpStart = flatpickr(startTimeInput, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            theme: "dark",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const startD = new Date(selectedDates[0]);
                    startD.setMinutes(startD.getMinutes() + 1);
                    fpEnd.set('minDate', startD);
                } else {
                    fpEnd.set('minDate', "today");
                }
                validateEndTime();
            }
        });

        // Ensure "today" minimum time is strictly enforced in flatpickr if selecting today's date
        function enforceMinTimeForToday(fpInstance) {
            const now = new Date();
            if (fpInstance.selectedDates.length > 0) {
                const selectedDate = fpInstance.selectedDates[0];
                if (selectedDate.toDateString() === now.toDateString()) {
                    fpInstance.set('minTime', now.getHours() + ':' + now.getMinutes());
                } else {
                    fpInstance.set('minTime', null);
                }
            } else {
                fpInstance.set('minTime', now.getHours() + ':' + now.getMinutes());
            }
        }

        fpStart.config.onOpen = [function() { enforceMinTimeForToday(fpStart); }];
        fpStart.config.onChange.push(function() { enforceMinTimeForToday(fpStart); });
        fpEnd.config.onOpen = [function() { enforceMinTimeForToday(fpEnd); }];
        fpEnd.config.onChange.push(function() { enforceMinTimeForToday(fpEnd); });
        
        // Initial setup
        enforceMinTimeForToday(fpStart);
        enforceMinTimeForToday(fpEnd);
    }
    
    function validateEndTime() {
        if (!endTimeInput || !startTimeInput) return;
        if (!endTimeInput.value) {
            endTimeInput.setCustomValidity('');
            return;
        }
        
        const endD = new Date(endTimeInput.value);
        let minD = new Date();
        
        if (startTimeInput.value) {
            minD = new Date(startTimeInput.value);
            minD.setMinutes(minD.getMinutes() + 1);
        }
        minD.setSeconds(0,0);
        endD.setSeconds(0,0);
        
        if (endD < minD) {
            endTimeInput.setCustomValidity(startTimeInput.value ? 'End time must be after the start time.' : 'End time cannot be in the past.');
        } else {
            endTimeInput.setCustomValidity('');
        }
    }

    // 8. Form Submission Logic
    const form = document.getElementById('createPollForm');
    const alertBox = document.getElementById('alertBox');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');

    function showAlert(message, isError = true) {
        alertBox.textContent = message;
        alertBox.className = `mb-6 p-4 rounded-xl text-sm flex items-start gap-3 backdrop-blur-md border ${isError ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-400 border-emerald-500/20'}`;
        alertBox.classList.remove('hidden');
        
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', isError ? 'alert-circle' : 'check-circle');
        icon.className = 'w-5 h-5 shrink-0 mt-0.5';
        alertBox.prepend(icon);
        lucide.createIcons();
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Custom validation check before submitting
        validateEndTime();
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        alertBox.classList.add('hidden');
        
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-busy', 'true');
        const btnText = document.getElementById('btnText');
        if (btnText) btnText.textContent = 'Launching Poll...';
        submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.remove('hidden'));
        
        const formData = new FormData(form);
        
        // Inject choice_mode dynamically if voting type is Custom
        const selectedVotingType = document.querySelector('input[name="voting_type"]:checked').value;
        if (selectedVotingType === 'Custom') {
            const choiceMode = document.querySelector('input[name="choice_mode"]:checked').value;
            formData.set('voting_type', choiceMode); // overrides Custom with Single/Multiple for backend processing
        }
        
        fetch((window.SecurePollBase || '') + '/polls/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return res.json();
            } else {
                throw new Error("Server returned non-JSON response. Check your session or server logs.");
            }
        })
        .then(data => {
            if (data.success) {
                showAlert('Poll created successfully! Redirecting...', false);
                setTimeout(() => {
                    window.location.href = data.redirect_url || `${window.SecurePollBase || ""}/polls/manage?id=${data.poll_id}`;
                }, 1500);
            } else {
                showAlert(data.message);
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-busy');
                if (btnText) btnText.textContent = 'Launch Poll';
                submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.add('hidden'));
            }
        })
        .catch(err => {
            showAlert(err.message || 'A network error occurred while creating the poll.');
            submitBtn.disabled = false;
            submitBtn.removeAttribute('aria-busy');
            if (btnText) btnText.textContent = 'Launch Poll';
            submitBtn.querySelectorAll('.animate-spin, #spinner').forEach(el => el.classList.add('hidden'));
        });
    });
});
</script>

<?php
$basePrefix = $basePrefix ?? (strpos($_SERVER['REQUEST_URI'] ?? '', '/SecurePoll/public') !== false ? '/SecurePoll/public' : '');
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>
