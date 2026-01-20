/**
 * Header Module
 * Handles year selector, search, and navigation interactions
 */

export function initHeader() {
    initYearSelector();
    initSearch();
}

/**
 * Year Selector Dropdown
 */
function initYearSelector() {
    const container = document.getElementById('yearSelectorContainer');
    if (!container) return;

    // Toggle dropdown
    const btn = container.querySelector('.year-selector-btn');
    if (btn) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            container.classList.toggle('active');
        });
    }

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (container && !container.contains(e.target)) {
            container.classList.remove('active');
        }
    });
}

/**
 * Expandable Search
 */
function initSearch() {
    const container = document.getElementById('searchContainer');
    const trigger = container?.querySelector('.search-trigger');
    const input = document.getElementById('animeSearch');

    if (!container || !input) return;

    // Open search
    trigger?.addEventListener('click', () => {
        container.classList.add('open');
        setTimeout(() => input.focus(), 100);
    });

    // Close on blur if empty
    input.addEventListener('blur', () => {
        if (!input.value.trim()) {
            container.classList.remove('open');
        }
    });

    // Search functionality
    let searchTimeout;
    input.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.toLowerCase().trim();

        searchTimeout = setTimeout(() => {
            filterAnimeCards(query);
        }, 300);
    });
}

/**
 * Filter anime cards by search query
 */
function filterAnimeCards(query) {
    const cards = document.querySelectorAll('.anime-card');

    cards.forEach(card => {
        const title = card.dataset.title?.toLowerCase() || '';
        const englishTitle = card.dataset.englishTitle?.toLowerCase() || '';

        if (!query || title.includes(query) || englishTitle.includes(query)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Update year (called from year selector links)
 */
window.updateYear = function (year) {
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    url.searchParams.delete('season');
    window.location.href = url.toString();
};
