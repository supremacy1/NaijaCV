import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const previewContainerSelector = '[data-cv-preview-container]';
let previewRequestPending = false;

function showPreviewNavigationError(container) {
    let message = container.querySelector('[data-preview-navigation-error]');

    if (!message) {
        message = document.createElement('p');
        message.dataset.previewNavigationError = '';
        message.setAttribute('role', 'alert');
        message.className = 'cv-preview-navigation-error';
        container.insertBefore(message, container.firstChild);
    }

    message.textContent = 'Could not load that CV template preview. Please try again.';
}

async function loadCvPreview(url, addHistoryEntry, focusDirection) {
    const container = document.querySelector(previewContainerSelector);

    if (!container || previewRequestPending) {
        return;
    }

    previewRequestPending = true;
    container.classList.add('is-preview-loading');
    container.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Preview request failed with status ${response.status}.`);
        }

        const documentFromResponse = new DOMParser().parseFromString(
            await response.text(),
            'text/html',
        );
        const replacement = documentFromResponse.querySelector(previewContainerSelector);

        if (!replacement) {
            if (response.redirected) {
                window.location.assign(response.url);
                return;
            }

            throw new Error('The preview response did not contain the preview content.');
        }

        container.innerHTML = replacement.innerHTML;
        document.title = documentFromResponse.title;

        if (addHistoryEntry) {
            window.history.pushState({ cvPreview: true }, '', url);
        }

        if (focusDirection) {
            const focusedArrow = container.querySelector(
                `[data-preview-direction="${focusDirection}"]`,
            );
            focusedArrow?.focus({ preventScroll: true });
        }
    } catch (error) {
        console.error('Unable to load the selected CV template preview.', error);
        showPreviewNavigationError(container);
    } finally {
        previewRequestPending = false;
        container.classList.remove('is-preview-loading');
        container.removeAttribute('aria-busy');
    }
}

const initialPreviewContainer = document.querySelector(previewContainerSelector);

if (initialPreviewContainer) {
    const currentHistoryState = window.history.state || {};
    window.history.replaceState(
        { ...currentHistoryState, cvPreview: true },
        '',
        window.location.href,
    );

    document.addEventListener('click', (event) => {
        const link = event.target instanceof Element
            ? event.target.closest('a.cv-preview-arrow')
            : null;

        if (
            !link ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const targetUrl = new URL(link.href, window.location.href);

        if (targetUrl.origin !== window.location.origin) {
            return;
        }

        event.preventDefault();
        loadCvPreview(targetUrl, true, link.dataset.previewDirection);
    });

    window.addEventListener('popstate', (event) => {
        if (event.state && event.state.cvPreview) {
            loadCvPreview(window.location.href, false, null);
            return;
        }

        window.location.reload();
    });
}
