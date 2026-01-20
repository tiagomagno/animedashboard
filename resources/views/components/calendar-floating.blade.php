<!-- Calendar Floating Widget -->
<div class="calendar-floating-widget" id="calendarFloating">
    <div class="calendar-header-toggle">
        <div class="calendar-title-toggle">
            <i class="ph-fill ph-calendar-check"></i>
            <span>Calendário Semanal</span>
        </div>
        <i class="ph-bold ph-caret-down toggle-icon-cal"></i>
    </div>
    
    <div class="calendar-days-tabs">
        <button class="day-tab" data-day="mon">Mon</button>
        <button class="day-tab active" data-day="tue">Tue</button>
        <button class="day-tab" data-day="wed">Wed</button>
        <button class="day-tab" data-day="thu">Thu</button>
        <button class="day-tab" data-day="fri">Fri</button>
        <button class="day-tab" data-day="sat">Sat</button>
        <button class="day-tab" data-day="sun">Sun</button>
        <button class="day-tab" data-day="completed">Completed</button>
    </div>
    
    <div class="calendar-content-daily">
        <div class="daily-anime-grid">
            <!-- Populated by JavaScript -->
            <div class="empty-day-message">
                <i class="ph ph-calendar-check"></i>
                <p>Carregando animes do dia...</p>
            </div>
        </div>
    </div>
</div>

<style>
@import url('/css/components/calendar-floating.css');
</style>

<script type="module">
import { initCalendarFloating } from '/js/modules/calendar-floating.js';

document.addEventListener('DOMContentLoaded', () => {
    initCalendarFloating();
});
</script>
