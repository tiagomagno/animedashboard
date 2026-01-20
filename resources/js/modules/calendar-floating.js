/**
 * Calendar Floating Widget Module
 * Handles daily calendar with day selection
 */

export function initCalendarFloating() {
    setupDayTabs();
    setupToggle();
    loadTodayAnimes();
}

/**
 * Setup day tabs functionality
 */
function setupDayTabs() {
    const tabs = document.querySelectorAll('.day-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active from all
            tabs.forEach(t => t.classList.remove('active'));
            // Add active to clicked
            tab.classList.add('active');
            // Load animes for selected day
            loadAnimesByDay(tab.dataset.day);
        });
    });
}

/**
 * Setup calendar toggle
 */
function setupToggle() {
    const header = document.querySelector('.calendar-header-toggle');
    const widget = document.querySelector('.calendar-floating-widget');

    if (!header || !widget) return;

    header.addEventListener('click', () => {
        widget.classList.toggle('closed');

        // Save state
        const isClosed = widget.classList.contains('closed');
        localStorage.setItem('calendarClosed', isClosed);
    });

    // Restore state
    const wasClosed = localStorage.getItem('calendarClosed') === 'true';
    if (wasClosed) {
        widget.classList.add('closed');
    }
}

/**
 * Load today's animes
 */
function loadTodayAnimes() {
    const today = new Date().toLocaleDateString('en-US', { weekday: 'short' }).toLowerCase();
    loadAnimesByDay(today);
}

/**
 * Load animes by day of week
 */
async function loadAnimesByDay(day) {
    const container = document.querySelector('.daily-anime-grid');
    if (!container) return;

    // Show loading
    container.innerHTML = '<div class="loading-indicator"><div class="spinner"></div></div>';

    try {
        // In a real implementation, this would fetch from API
        // For now, we'll use placeholder data
        const animes = getAnimesForDay(day);

        if (animes.length === 0) {
            container.innerHTML = `
                <div class="empty-day-message">
                    <i class="ph ph-calendar-x"></i>
                    <p>Nenhum anime agendado para este dia</p>
                </div>
            `;
            return;
        }

        container.innerHTML = animes.map(anime => `
            <div class="daily-anime-card" onclick="openModal(this)"
                 data-id="${anime.id}"
                 data-title="${anime.title}"
                 data-image="${anime.image}"
                 data-synopsis="${anime.synopsis || ''}"
                 data-score="${anime.score || 0}"
                 data-members="${anime.members || 0}">
                <div class="daily-anime-badge">New Episode</div>
                <img src="${anime.image}" alt="${anime.title}" class="daily-anime-poster" loading="lazy">
                <div class="daily-anime-info">
                    <div class="daily-anime-title">${anime.title}</div>
                    <div class="daily-anime-time">${anime.time || 'TBA'}</div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error loading animes:', error);
        container.innerHTML = `
            <div class="empty-day-message">
                <i class="ph ph-warning"></i>
                <p>Erro ao carregar animes</p>
            </div>
        `;
    }
}

/**
 * Get animes for specific day (placeholder)
 * In production, this would fetch from backend
 */
function getAnimesForDay(day) {
    // This is placeholder data
    // In real implementation, fetch from /api/calendar/day/{day}
    const daysMap = {
        'mon': [],
        'tue': [],
        'wed': [],
        'thu': [],
        'fri': [],
        'sat': [],
        'sun': [],
        'completed': []
    };

    return daysMap[day] || [];
}

/**
 * Global toggle function (called from HTML)
 */
window.toggleCalendarFloating = function () {
    const widget = document.querySelector('.calendar-floating-widget');
    if (widget) {
        widget.classList.toggle('closed');
    }
};
