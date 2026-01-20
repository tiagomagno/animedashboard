/**
 * Modal Module
 * Handles anime detail modal and settings modal
 */

export function initModals() {
    initAnimeModal();
    initSettingsModal();
    initKeyboardShortcuts();
}

/**
 * Anime Detail Modal
 */
function initAnimeModal() {
    // Modal is opened via onclick="openModal(this)" on cards
    // Handled globally below
}

/**
 * Settings Modal (Streamer Mode)
 */
function initSettingsModal() {
    // Restore saved camera position
    const savedPos = localStorage.getItem('streamerCamPos');
    if (savedPos) {
        setCameraPosition(savedPos);
    }
}

/**
 * Keyboard shortcuts
 */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
            closeSettingsModal();
        }
    });
}

/**
 * Open anime modal
 */
window.openModal = function (element) {
    const data = element.dataset;
    const modal = document.getElementById('animeModal');
    if (!modal) return;

    // Populate modal content
    document.getElementById('modalTitle').textContent = data.title;

    const posterImg = document.getElementById('modalPoster');
    if (posterImg) {
        posterImg.src = data.image || '';
        posterImg.classList.remove('loaded');
        posterImg.onload = () => posterImg.classList.add('loaded');
    }

    // Subtitle
    const sub = document.getElementById('modalSubtitle');
    if (sub) {
        if (data.englishTitle && data.englishTitle !== data.title) {
            sub.textContent = data.englishTitle;
            sub.style.display = 'block';
        } else {
            sub.style.display = 'none';
        }
    }

    // Score and members
    const scoreVal = parseFloat(data.score) || 0;
    const scoreSpan = document.querySelector('#modalScore span');
    if (scoreSpan) {
        scoreSpan.textContent = scoreVal.toFixed(2);
        document.getElementById('modalScore').style.display = scoreVal ? 'flex' : 'none';
    }

    const membersVal = parseInt(data.members) || 0;
    const membersSpan = document.querySelector('#modalMembers span');
    if (membersSpan) {
        membersSpan.textContent = formatMembers(membersVal);
    }

    // Other fields
    const typeEl = document.getElementById('modalType');
    if (typeEl) typeEl.textContent = (data.type || 'TV').toUpperCase();

    const synopsisEl = document.getElementById('modalSynopsis');
    if (synopsisEl) {
        synopsisEl.textContent = data.synopsis || 'Sinopse não disponível.';
        synopsisEl.classList.remove('expanded');
    }

    const readMoreBtn = document.getElementById('readMoreBtn');
    if (readMoreBtn && data.synopsis && data.synopsis.length > 250) {
        readMoreBtn.style.display = 'inline-block';
        readMoreBtn.textContent = 'Ver mais';
    } else if (readMoreBtn) {
        readMoreBtn.style.display = 'none';
    }

    // Additional info
    setModalField('modalEpisodes', data.episodes ? `${data.episodes} eps` : '?');
    setModalField('modalYear', data.year);
    setModalField('modalRatingBadge', data.rating ? data.rating.replace('_', '-').toUpperCase() : 'N/A');
    setModalField('modalStudio', data.studio || 'N/A');
    setModalField('modalSource', data.source ? data.source.replace('_', ' ') : 'N/A');
    setModalField('modalGenres', data.genres || 'N/A');

    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

/**
 * Close anime modal
 */
window.closeModal = function (event) {
    if (event && event.target !== event.currentTarget) return;

    const modal = document.getElementById('animeModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
};

/**
 * Toggle synopsis expansion
 */
window.toggleSynopsis = function () {
    const el = document.getElementById('modalSynopsis');
    const btn = document.getElementById('readMoreBtn');

    if (!el || !btn) return;

    if (el.classList.contains('expanded')) {
        el.classList.remove('expanded');
        btn.textContent = 'Ver mais';
    } else {
        el.classList.add('expanded');
        btn.textContent = 'Ver menos';
    }
};

/**
 * Settings Modal
 */
window.toggleSettingsModal = function () {
    const modal = document.getElementById('settingsModal');
    if (!modal) return;

    const isActive = modal.classList.contains('active');

    if (isActive) {
        modal.classList.remove('active');
        modal.style.display = 'none';
    } else {
        modal.classList.add('active');
        modal.style.display = 'flex';

        const currentPos = localStorage.getItem('streamerCamPos') || 'off';
        updateActiveCameraOption(currentPos);
    }
};

window.closeSettingsModal = function (event) {
    if (event) event.stopPropagation();

    const modal = document.getElementById('settingsModal');
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none';
    }
};

/**
 * Camera position management
 */
window.setCameraPosition = function (pos) {
    document.body.classList.remove(
        'has-cam-top-left', 'has-cam-top-right',
        'has-cam-bottom-left', 'has-cam-bottom-right',
        'has-cam-center-left', 'has-cam-center-right'
    );

    if (pos !== 'off') {
        document.body.classList.add(`has-cam-${pos}`);
    }

    localStorage.setItem('streamerCamPos', pos);
    updateActiveCameraOption(pos);
};

function updateActiveCameraOption(pos) {
    document.querySelectorAll('.camera-option').forEach(el => {
        el.classList.remove('active');
        if (el.dataset.pos === pos) {
            el.classList.add('active');
        }
    });
}

/**
 * Helper functions
 */
function formatMembers(count) {
    if (count >= 1000000) {
        return (count / 1000000).toFixed(1) + 'M';
    } else if (count >= 1000) {
        return Math.floor(count / 1000) + 'K';
    }
    return count;
}

function setModalField(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}
