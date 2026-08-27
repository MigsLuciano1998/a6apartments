function sidebarNav() {
    const sidebar = document.querySelector('[data-sidebar]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const openButton = document.querySelector('[data-open-sidebar]');
    const closeButton = document.querySelector('[data-close-sidebar]');

    if (!sidebar || !openButton) {
        return;
    }

    const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

    const setOpen = (open) => {
        if (open) {
            sidebar.setAttribute('data-open', '');
            backdrop?.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            openButton.setAttribute('aria-expanded', 'true');
        } else {
            sidebar.removeAttribute('data-open');
            backdrop?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            openButton.setAttribute('aria-expanded', 'false');
        }
    };

    openButton.addEventListener('click', () => setOpen(true));
    closeButton?.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (!isDesktop()) {
                setOpen(false);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (isDesktop()) {
            setOpen(false);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => sidebarNav());
