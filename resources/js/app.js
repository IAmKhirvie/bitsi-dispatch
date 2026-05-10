// Dark mode initialization — runs before paint to prevent flash
// Uses 'appearance' key to stay in sync with the Inertia Vue app
// Defaults to light mode when no preference is saved

// Force light mode on unauthenticated pages (welcome, login, register, forgot password)
function isAuthPage() {
    const path = window.location.pathname;
    return path === '/' || path.startsWith('/login') || path.startsWith('/register') || path.startsWith('/forgot-password') || path.startsWith('/reset-password');
}

function initializeTheme() {
    // Always force light mode on login, signup, and pre-auth homepage
    if (isAuthPage()) {
        document.documentElement.classList.remove('dark');
        return;
    }

    const saved = localStorage.getItem('appearance');
    if (saved === 'dark') {
        document.documentElement.classList.add('dark');
    } else if (saved === 'light') {
        document.documentElement.classList.remove('dark');
    } else if (saved === 'system') {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    } else {
        // Default: light mode
        document.documentElement.classList.remove('dark');
    }
}

// Initialize on load
initializeTheme();

// Listen for system theme changes (only when user chose 'system')
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const saved = localStorage.getItem('appearance');
    if (saved === 'system') {
        initializeTheme();
    }
});

// Expose for Alpine.js usage — also uses 'appearance' key
window.updateTheme = function(value) {
    localStorage.setItem('appearance', value);
    if (value === 'system') {
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
};