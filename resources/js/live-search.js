function liveSearch(root = document) {
    const input = root.querySelector('[data-live-search]');
    const target = root.querySelector('[data-live-search-results]');

    if (!input || !target) {
        return;
    }

    let timer;
    let controller;

    const load = (query, pageUrl = null) => {
        controller?.abort();
        controller = new AbortController();

        window.axios
            .get(pageUrl ?? window.location.pathname, {
                params: pageUrl ? undefined : { q: query },
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

document.addEventListener('DOMContentLoaded', () => liveSearch());
