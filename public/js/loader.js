/**
 * SecurePoll Unified Loading & Transition Manager
 * Handles:
 * 1. Initial page branded loading screen dismissal
 * 2. Top progress bar for page navigation
 * 3. Action-level button loading states and double-submit prevention
 * 4. Back/Forward cache (bfcache) restoration safety
 */
(function() {
    'use strict';

    let progressTimer = null;
    let progressValue = 0;

    const SecurePollLoader = {
        /**
         * Dismiss the initial page loading overlay smoothly
         */
        dismissInitialLoader: function() {
            const loader = document.getElementById('securepoll-page-loader');
            if (!loader || loader.classList.contains('loader-hidden')) return;

            loader.classList.add('loader-hidden');
            setTimeout(function() {
                if (loader && loader.parentNode) {
                    loader.style.display = 'none';
                }
            }, 300);
        },

        /**
         * Start top progress bar for navigation feedback
         */
        startProgress: function() {
            const bar = document.getElementById('securepoll-top-progress');
            if (!bar) return;

            clearInterval(progressTimer);
            progressValue = 15;
            bar.style.width = '15%';
            bar.style.opacity = '1';

            progressTimer = setInterval(function() {
                if (progressValue < 85) {
                    progressValue += Math.random() * 12 + 4;
                    if (progressValue > 85) progressValue = 85;
                    bar.style.width = progressValue + '%';
                }
            }, 180);
        },

        /**
         * Complete and hide top progress bar
         */
        finishProgress: function() {
            const bar = document.getElementById('securepoll-top-progress');
            if (!bar) return;

            clearInterval(progressTimer);
            progressValue = 100;
            bar.style.width = '100%';

            setTimeout(function() {
                bar.style.opacity = '0';
                setTimeout(function() {
                    bar.style.width = '0%';
                }, 200);
            }, 120);
        },

        /**
         * Set button loading state with accessibility semantics
         * @param {HTMLElement} btn The button element
         * @param {boolean} isLoading True to set loading, false to restore
         * @param {string} [loadingText] Text to show while loading
         */
        setButtonLoading: function(btn, isLoading, loadingText) {
            if (!btn) return;

            if (isLoading) {
                if (btn.disabled) return;
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');

                const text = loadingText || 'Processing...';
                btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin shrink-0 inline-block align-middle mr-2"></i>' +
                                '<span class="align-middle">' + text + '</span>';
                if (window.lucide) {
                    window.lucide.createIcons({ root: btn });
                }
            } else {
                if (btn.dataset.originalHtml) {
                    btn.innerHTML = btn.dataset.originalHtml;
                    delete btn.dataset.originalHtml;
                }
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                if (window.lucide) {
                    window.lucide.createIcons({ root: btn });
                }
            }
        }
    };

    window.SecurePollLoader = SecurePollLoader;

    // 1. Initial Page Load Dismissal
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(SecurePollLoader.dismissInitialLoader, 80);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(SecurePollLoader.dismissInitialLoader, 80);
        });
        window.addEventListener('load', function() {
            SecurePollLoader.dismissInitialLoader();
        });
    }

    // Safety fallback: ensure loader is dismissed after max 1500ms
    setTimeout(SecurePollLoader.dismissInitialLoader, 1500);

    // 2. Navigation Link Interception for Top Progress Bar
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link || !link.href) return;

        // Skip modified clicks (Ctrl, Cmd, Shift, Alt, middle click)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

        // Skip anchors, javascript, tel, mailto, target="_blank", or download
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
        if (link.target && link.target !== '_self') return;
        if (link.hasAttribute('download')) return;

        // Check if same origin and different URL
        try {
            const destUrl = new URL(link.href, window.location.href);
            if (destUrl.origin !== window.location.origin) return;
            if (destUrl.pathname === window.location.pathname && destUrl.search === window.location.search && destUrl.hash !== window.location.hash) {
                return; // Hash change only on same page
            }
            SecurePollLoader.startProgress();
        } catch(err) {}
    });

    // 3. Handle Back/Forward Cache (bfcache) & History Navigation
    window.addEventListener('pageshow', function(event) {
        SecurePollLoader.dismissInitialLoader();
        SecurePollLoader.finishProgress();

        // Restore any disabled submit buttons left in busy state
        document.querySelectorAll('button[aria-busy="true"]').forEach(function(btn) {
            SecurePollLoader.setButtonLoading(btn, false);
        });
    });

    // Start progress on regular unload
    window.addEventListener('beforeunload', function() {
        SecurePollLoader.startProgress();
    });

})();
