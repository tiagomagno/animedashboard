/**
 * Calendar Module
 * Handles bottom calendar widget
 */

export function initCalendar() {
    setupCalendarContent();
}

/**
 * Setup calendar with today's date
 */
function setupCalendarContent() {
    const calContainer = document.querySelector('.calendar-days');
    if (!calContainer) return;

    // Clear default content
    calContainer.innerHTML = '';
    calContainer.style.display = 'flex';
    calContainer.style.gap = '2rem';
    calContainer.style.height = 'auto';
    calContainer.style.minHeight = '150px';

    // Get today's date
    const today = new Date();
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    const todayStr = today.toLocaleDateString('pt-BR', options);

    // Create today column
    const todayDiv = document.createElement('div');
    todayDiv.className = 'day-column';
    todayDiv.style.flex = '1';
    todayDiv.style.background = 'transparent';

    todayDiv.innerHTML = `
        <div class="day-header" style="text-align: left; font-size: 1.1rem; margin-bottom: 1.5rem; color: #fff; text-transform: capitalize; display: flex; align-items: center; gap: 10px;">
            <i class="ph-fill ph-calendar-check" style="color: var(--primary-color);"></i>
            Animes de Hoje <span style="color: #666; font-size: 0.8rem; font-weight: 400; text-transform: none;">(${todayStr})</span>
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; background: #1a1a1a; padding: 2rem; border-radius: 12px; border: 1px dashed #333;">
            <div style="color: #888; font-size: 0.9rem;">
                Confira a grade completa de lançamentos e horários.
            </div>
            <a href="${getCalendarRoute()}" style="background: var(--primary-color); color: #000; padding: 10px 24px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 0.85rem; transition: transform 0.2s; display: inline-flex; align-items: center; gap: 8px;">
                VER GRADE DE HOJE <i class="ph-bold ph-arrow-right"></i>
            </a>
        </div>
    `;

    calContainer.appendChild(todayDiv);
}

/**
 * Get calendar route (helper)
 */
function getCalendarRoute() {
    // This should match Laravel route('calendar.index')
    return '/calendar';
}

/**
 * Toggle calendar visibility
 */
window.toggleBottomCalendar = function (e) {
    if (e) e.preventDefault();
    const cal = document.getElementById('bottomCalendar');
    if (cal) {
        cal.classList.toggle('closed');
    }
};
