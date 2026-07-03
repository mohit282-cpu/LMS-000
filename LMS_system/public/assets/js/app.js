(() => {
    const storedTheme = localStorage.getItem('lms-theme');
    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const initialTheme = storedTheme || preferredTheme;

    document.documentElement.setAttribute('data-bs-theme', initialTheme);

    const toggle = document.querySelector('[data-theme-toggle]');

    if (toggle) {
        toggle.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('lms-theme', next);
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (! window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
})();

