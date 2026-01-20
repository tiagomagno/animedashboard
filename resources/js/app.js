/**
 * AnimeDashboard - Main Application Entry Point
 * Modular JavaScript Architecture
 */

import { initHeader } from './modules/header.js';
import { initFilters } from './modules/filters.js';
import { initCalendar } from './modules/calendar.js';
import { initModals } from './modules/modal.js';

/**
 * Initialize application
 */
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 AnimeDashboard initialized');

    initHeader();
    initFilters();
    initCalendar();
    initModals();
    initAlerts();
});

/**
 * Auto-hide alerts
 */
function initAlerts() {
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

/**
 * Export for debugging
 */
if (import.meta.env.DEV) {
    window.AnimeDashboard = {
        version: '2.0.0',
        modules: { initHeader, initFilters, initCalendar, initModals }
    };
}
