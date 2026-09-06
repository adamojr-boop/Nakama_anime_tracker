import 'bootstrap';
import AOS from 'aos';

AOS.init();

const THEME_KEY = 'nakama-theme';

function syncThemeIcon(theme) {
    const icon = document.querySelector('[data-theme-icon]');
    if (!icon) return;
    icon.classList.toggle('bi-moon-stars-fill', theme === 'light');
    icon.classList.toggle('bi-sun-fill', theme === 'dark');
}

document.addEventListener('DOMContentLoaded', () => {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    syncThemeIcon(currentTheme);

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const nextTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', nextTheme);
        localStorage.setItem(THEME_KEY, nextTheme);
        syncThemeIcon(nextTheme);
    });
});