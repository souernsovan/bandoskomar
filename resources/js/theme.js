/**
 * Theme Switcher - Light / Dark mode
 * Applies theme from localStorage or system preference, prevents flash
 */
const ThemeSwitcher = {
    KEY: 'theme',
    LIGHT: 'light',
    DARK: 'dark',

    init() {
        const theme = this.getStored();
        this.apply(theme);
    },

    getStored() {
        return localStorage.getItem(this.KEY) || this.LIGHT;
    },

    setStored(theme) {
        localStorage.setItem(this.KEY, theme);
    },

    getCurrent() {
        return document.documentElement.classList.contains('dark') ? this.DARK : this.LIGHT;
    },

    apply(theme) {
        if (theme === this.DARK) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    toggle() {
        const next = this.getCurrent() === this.DARK ? this.LIGHT : this.DARK;
        this.setStored(next);
        this.apply(next);
        // this.logThemeChange(next);
        return next;
    },

    logThemeChange(theme) {
        const url = window.location.origin + '/admin/theme/log';
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ theme }),
                credentials: 'same-origin',
            }).catch(() => {});
        }
    },

    set(theme) {
        if (theme !== this.LIGHT && theme !== this.DARK) return;
        this.setStored(theme);
        this.apply(theme);
    }
};

// Apply immediately on load (run before DOM ready to prevent flash)
ThemeSwitcher.init();

// Expose globally for inline handlers
window.ThemeSwitcher = ThemeSwitcher;
