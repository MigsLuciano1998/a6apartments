function liveSearchWidget(root) {
    const input = root.querySelector('[data-live-search]');
    const target = root.querySelector('[data-live-search-results]');

    if (!input || !target) {
        return;
    }

    const param = root.dataset.searchParam || input.getAttribute('name') || 'q';
    const table = root.dataset.searchTable || '';
    const pageName = root.dataset.pageName || 'page';

    let timer;
    let controller;

    const load = (query, pageUrl = null) => {
        controller?.abort();
        controller = new AbortController();

        const url = new URL(pageUrl ?? window.location.href, window.location.origin);

        if (!pageUrl) {
            url.searchParams.set(param, query);
            url.searchParams.delete(pageName);
        }

        if (table) {
            url.searchParams.set('_table', table);
        }

        window.axios
            .get(url.pathname + url.search, {
                signal: controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then((response) => {
                target.innerHTML = response.data;
            })
            .catch((error) => {
                if (error.code !== 'ERR_CANCELED') {
                    console.error(error);
                }
            });
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => load(input.value.trim()), 250);
    });

    input.form?.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(timer);
        load(input.value.trim());
    });

    target.addEventListener('click', (event) => {
        const link = event.target.closest('a');

        if (!link || !target.contains(link) || !link.href || !link.closest('nav')) {
            return;
        }

        event.preventDefault();
        load(input.value.trim(), link.href);
    });
}

function liveSearch(root = document) {
    const widgets = [...root.querySelectorAll('[data-live-search-table]')];

    if (widgets.length) {
        widgets.forEach((widget) => liveSearchWidget(widget));
        return;
    }

    const input = root.querySelector('[data-live-search]');
    const target = root.querySelector('[data-live-search-results]');

    if (input && target) {
        liveSearchWidget(root);
    }
}

document.addEventListener('DOMContentLoaded', () => liveSearch());
