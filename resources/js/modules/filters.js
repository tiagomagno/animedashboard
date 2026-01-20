/**
 * Filters Module
 * Handles filter dropdowns and genre filtering
 */

export function initFilters() {
    initMoreFiltersDropdown();
    initGenreFilter();
}

/**
 * More Filters (+) Dropdown
 */
function initMoreFiltersDropdown() {
    const btn = document.querySelector('.btn-more-filters');
    const menu = document.getElementById('moreFiltersMenu');

    if (!btn || !menu) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = menu.style.display === 'block';

        menu.style.display = isOpen ? 'none' : 'block';
        btn.querySelector('i').style.color = isOpen ? '#666' : 'var(--primary-color)';
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
            menu.style.display = 'none';
            btn.querySelector('i').style.color = '#666';
        }
    });
}

/**
 * Genre Filter (Client-side)
 */
function initGenreFilter() {
    // Genre tags are created with onclick="filterByGenre(...)"
    // This is handled globally below
}

/**
 * Filter anime cards by genre
 */
window.filterByGenre = function (genre, element) {
    // Update visual state
    document.querySelectorAll('.genre-tag').forEach(tag => {
        tag.style.background = '#1a1a1a';
        tag.style.color = '#888';
        tag.style.borderColor = 'transparent';
    });

    element.style.background = '#222';
    element.style.color = '#fff';
    element.style.borderColor = 'var(--primary-color)';

    // Filter cards
    const cards = document.querySelectorAll('.anime-card');

    cards.forEach(card => {
        const genresStr = card.dataset.genres || '';
        const genres = genresStr.split(',').map(s => s.trim());

        if (genre === 'all' || genres.includes(genre)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
};

/**
 * Toggle filter (hide kids/adult)
 */
window.toggleFilter = function (filterName, isChecked) {
    const url = new URL(window.location.href);
    url.searchParams.set(filterName, isChecked ? '1' : '0');
    window.location.href = url.toString();
};
