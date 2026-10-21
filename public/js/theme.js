/**
 * SecurePoll Unified Theme Engine
 * Authoritative single source of truth for theme preference across all pages.
 * Supports: 'light', 'dark', 'system'
 */
(function() {
    let currentPreference = 'system';
    let saveRequestId = 0;

    function getBasePrefix() {
        if (typeof window.SecurePollBase !== 'undefined') {
            return window.SecurePollBase;
        }
        return window.location.pathname.startsWith('/SecurePoll/public') ? '/SecurePoll/public' : '';
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function applyDomTheme(preference) {
        if (preference === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme', 'dark');
        } else if (preference === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            // 'system' mode: follow operating system preference
            document.documentElement.setAttribute('data-theme', 'system');
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }

    function updateThemePill(preference) {
        const themePills = document.querySelectorAll('#theme-active-pill');
        const index = ['light', 'system', 'dark'].indexOf(preference);
        if (index !== -1) {
            themePills.forEach(pill => {
                pill.style.transform = `translateX(${index * 100}%)`;
            });
        }

        // Update ARIA pressed state for accessible screen reader feedback
        document.querySelectorAll('.theme-btn').forEach(btn => {
            const btnTheme = btn.getAttribute('data-theme');
            const isSelected = (btnTheme === preference);
            btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            if (isSelected) {
                btn.classList.add('text-teal-600', 'dark:text-teal-300');
                btn.classList.remove('text-slate-500');
            } else {
                btn.classList.remove('text-teal-600', 'dark:text-teal-300');
                btn.classList.add('text-slate-500');
            }
        });
    }

    function updateSettingsCards(preference) {
        const cards = document.querySelectorAll('.settings-theme-btn');
        if (!cards.length) return;

        cards.forEach(card => {
            const cardTheme = card.getAttribute('data-settings-theme');
            const isActive = cardTheme === preference;
            const icon = card.querySelector('.theme-icon');
            const label = card.querySelector('.theme-label');
            const desc = card.querySelector('.theme-desc');

            card.setAttribute('aria-pressed', isActive ? 'true' : 'false');

            if (isActive) {
                card.className = 'settings-theme-btn flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all border-teal-600 bg-teal-50 dark:bg-teal-500/10 shadow-xs dark:shadow-none';
                if (icon) icon.className = 'w-5 h-5 theme-icon text-teal-600 dark:text-teal-400';
                if (label) label.className = 'font-bold theme-label text-teal-900 dark:text-teal-300';
                if (desc) desc.className = 'text-xs theme-desc text-teal-700 dark:text-teal-400/80';
            } else {
                card.className = 'settings-theme-btn flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 transition-all border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-xs dark:hover:shadow-none';
                if (icon) icon.className = 'w-5 h-5 theme-icon text-slate-500 dark:text-slate-400';
                if (label) label.className = 'font-bold theme-label text-slate-700 dark:text-slate-300';
                if (desc) desc.className = 'text-xs theme-desc text-slate-500 dark:text-slate-500';
            }
        });
    }

    function updateControls(preference) {
        updateThemePill(preference);
        updateSettingsCards(preference);
    }

    function savePreferenceToServer(preference) {
        const reqId = ++saveRequestId;
        const csrf = getCsrfToken();

        fetch(getBasePrefix() + '/settings/theme', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'theme=' + encodeURIComponent(preference) + '&csrf_token=' + encodeURIComponent(csrf),
            keepalive: true
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (reqId !== saveRequestId) return;
            if (!data.success) {
                // Not authenticated or settings unavailable - client state remains authoritative
            }
        })
        .catch(err => {
            // Network failure or unauthenticated - client state remains intact
        });
    }

    function setTheme(newTheme, options = {}) {
        if (!['light', 'dark', 'system'].includes(newTheme)) return;

        currentPreference = newTheme;

        // 1. Optimistic UI update
        applyDomTheme(newTheme);
        updateControls(newTheme);

        // 2. Client persistence (localStorage & Cookie for server anti-FOUC)
        try {
            localStorage.setItem('securepoll_theme', newTheme);
        } catch(e) {}
        document.cookie = 'securepoll_theme=' + encodeURIComponent(newTheme) + '; path=/; max-age=31536000; SameSite=Lax';

        // 3. Dispatch event for DatePickers, charts, and custom listeners
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));

        // 4. Server persistence (unless skipServer is set)
        if (!options.skipServer) {
            savePreferenceToServer(newTheme);
        }
    }

    function init(serverTheme) {
        // Resolve initial preference:
        // Priority: serverTheme if valid, then localStorage, then cookie, default to 'system'
        let preference = serverTheme;
        if (!preference || !['light', 'dark', 'system'].includes(preference)) {
            try {
                const stored = localStorage.getItem('securepoll_theme');
                if (stored && ['light', 'dark', 'system'].includes(stored)) {
                    preference = stored;
                }
            } catch(e) {}
        }
        if (!preference || !['light', 'dark', 'system'].includes(preference)) {
            const cookieMatch = document.cookie.match(/(?:^|;\s*)securepoll_theme=([^;]+)/);
            if (cookieMatch && ['light', 'dark', 'system'].includes(cookieMatch[1])) {
                preference = cookieMatch[1];
            }
        }
        if (!preference || !['light', 'dark', 'system'].includes(preference)) {
            preference = 'system';
        }

        currentPreference = preference;
        applyDomTheme(preference);

        // Synchronize client storage
        try {
            localStorage.setItem('securepoll_theme', preference);
        } catch(e) {}
        document.cookie = 'securepoll_theme=' + encodeURIComponent(preference) + '; path=/; max-age=31536000; SameSite=Lax';

        // Bind quick switcher buttons
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                setTheme(this.dataset.theme);
            };
        });

        // Bind settings theme cards if present
        document.querySelectorAll('.settings-theme-btn').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const themeVal = this.getAttribute('data-settings-theme');
                if (themeVal) setTheme(themeVal);
            };
        });

        // Initialize pill and cards after layout settles
        setTimeout(() => {
            const themePills = document.querySelectorAll('#theme-active-pill');
            themePills.forEach(pill => pill.style.transition = 'none');
            updateControls(preference);
            setTimeout(() => {
                themePills.forEach(pill => pill.style.transition = 'transform 0.25s cubic-bezier(0.4, 0, 0.2, 1)');
            }, 50);
        }, 50);
    }

    // OS Theme change listener
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (currentPreference === 'system') {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: 'system', isDark: e.matches } }));
            }
        });
    }

    // Cross-tab synchronization
    window.addEventListener('storage', function(e) {
        if (e.key === 'securepoll_theme' && e.newValue && ['light', 'dark', 'system'].includes(e.newValue)) {
            currentPreference = e.newValue;
            applyDomTheme(e.newValue);
            updateControls(e.newValue);
            document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: e.newValue } }));
        }
    });

    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            init();
        });
    } else {
        init();
    }

    // Expose API
    window.SecurePollTheme = {
        init: init,
        setTheme: setTheme,
        getTheme: function() { return currentPreference; },
        applyDomTheme: applyDomTheme,
        updateControls: updateControls
    };
})();
