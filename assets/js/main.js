/**
 * SND Media — Primary Client-Side JavaScript
 * Implements category filtering, modal workflows, highlights toggle & broadcast player switcher
 */

document.addEventListener('DOMContentLoaded', () => {
  // Pre-activate category tab from URL parameter (?cat=...) or rewritten path (/category/{cat})
  const urlParams = new URLSearchParams(window.location.search);
  const pathMatch = window.location.pathname.match(/\/category\/([a-z0-9-]+)\/?$/i);
  const catParam = urlParams.get('cat') || (pathMatch ? pathMatch[1].toLowerCase() : null);
  if (catParam) {
    filterCategory(catParam);
    const deskSection = document.getElementById('news-desk');
    if (deskSection) {
      deskSection.scrollIntoView({ behavior: 'smooth' });
    }
  }

  // Keyboard accessibility: close modal on Escape
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeRentalModal();
    }
  });

  // Modal backdrop click listener
  const modal = document.getElementById('rental-modal');
  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeRentalModal();
      }
    });
  }
});

/**
 * Toggle Key Takeaways / 3-Bullet Highlights Drawer
 */
function toggleTakeaways() {
  const drawer = document.getElementById('takeaways-drawer');
  const btnText = document.getElementById('takeaways-btn-text');
  const btnIcon = document.getElementById('takeaways-btn-icon');
  if (!drawer) return;

  if (drawer.classList.contains('hidden')) {
    drawer.classList.remove('hidden');
    if (btnText) btnText.textContent = 'Hide Key Takeaways';
    if (btnIcon) {
      btnIcon.classList.remove('fa-chevron-down');
      btnIcon.classList.add('fa-chevron-up');
    }
  } else {
    drawer.classList.add('hidden');
    if (btnText) btnText.textContent = 'Show Key Takeaways';
    if (btnIcon) {
      btnIcon.classList.remove('fa-chevron-up');
      btnIcon.classList.add('fa-chevron-down');
    }
  }
}

/**
 * Filter News Desk Continuous Articles by Category
 * @param {string} category 
 * @param {HTMLElement|null} tabEl 
 */
function filterCategory(category, tabEl = null) {
  const cards = document.querySelectorAll('.news-desk-card');
  const tabs = document.querySelectorAll('.category-tab-btn');

  // Update tab visual states
  tabs.forEach(tab => {
    const tabCat = tab.getAttribute('data-category-target');
    if (tabCat === category) {
      tab.classList.remove('bg-slate-900', 'text-slate-400');
      tab.classList.add('bg-amber-500', 'text-slate-950', 'font-bold');
    } else {
      tab.classList.remove('bg-amber-500', 'text-slate-950', 'font-bold');
      tab.classList.add('bg-slate-900', 'text-slate-400');
    }
  });

  // Filter article cards
  let visibleCount = 0;
  cards.forEach(card => {
    const cardCat = card.getAttribute('data-category');
    if (category === 'all' || cardCat === category) {
      card.classList.remove('hidden');
      visibleCount++;
    } else {
      card.classList.add('hidden');
    }
  });

  // Handle empty state banner if no articles in category
  const emptyState = document.getElementById('news-desk-empty');
  if (emptyState) {
    if (visibleCount === 0) {
      emptyState.classList.remove('hidden');
    } else {
      emptyState.classList.add('hidden');
    }
  }
}

/**
 * Open Commercial Space Rental Modal
 * @param {string} facilityName 
 */
function openRentalModal(facilityName = '') {
  const modal = document.getElementById('rental-modal');
  if (!modal) return;

  const facilitySelect = document.getElementById('modal-facility');
  if (facilitySelect && facilityName) {
    for (let i = 0; i < facilitySelect.options.length; i++) {
      if (facilitySelect.options[i].text.toLowerCase().includes(facilityName.toLowerCase())) {
        facilitySelect.selectedIndex = i;
        break;
      }
    }
  }

  const form = document.getElementById('rental-enquiry-form');
  const successBox = document.getElementById('modal-success-box');
  const alertBox = document.getElementById('modal-alert');
  if (form) form.classList.remove('hidden');
  if (successBox) successBox.classList.add('hidden');
  if (alertBox) {
    alertBox.classList.add('hidden');
    alertBox.textContent = '';
  }

  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.body.style.overflow = 'hidden';
}

/**
 * Close Commercial Space Rental Modal
 */
function closeRentalModal() {
  const modal = document.getElementById('rental-modal');
  if (modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
  }
}

/**
 * Toggle Mobile Navigation Drawer
 */
function toggleMobileMenu() {
  const drawer = document.getElementById('mobile-nav-drawer');
  const icon = document.getElementById('mobile-menu-icon');
  if (!drawer) return;

  if (drawer.classList.contains('hidden')) {
    drawer.classList.remove('hidden');
    if (icon) {
      icon.classList.remove('fa-bars');
      icon.classList.add('fa-xmark');
    }
  } else {
    drawer.classList.add('hidden');
    if (icon) {
      icon.classList.remove('fa-xmark');
      icon.classList.add('fa-bars');
    }
  }
}

/**
 * Toggle Marquee Ticker Animation Pause/Play
 */
function toggleTickerPlay() {
  const marquee = document.getElementById('ticker-marquee');
  const icon = document.getElementById('ticker-play-icon');
  if (!marquee) return;

  const currentPlayState = window.getComputedStyle(marquee).animationPlayState;
  if (currentPlayState === 'paused' || marquee.style.animationPlayState === 'paused') {
    marquee.style.animationPlayState = 'running';
    if (icon) {
      icon.className = 'fa-solid fa-pause text-[10px]';
    }
  } else {
    marquee.style.animationPlayState = 'paused';
    if (icon) {
      icon.className = 'fa-solid fa-play text-[10px]';
    }
  }
}

function switchYtVideo(videoId, title, badge, el) {
  const player = document.getElementById('main-youtube-player');
  const titleEl = document.getElementById('player-title');
  const badgeEl = document.getElementById('player-badge');

  if (player) {
    player.src = `https://www.youtube-nocookie.com/embed/${videoId}?rel=0&autoplay=1`;
  }
  if (titleEl && title) titleEl.textContent = title;
  if (badgeEl && badge) badgeEl.textContent = badge;

  // Toggle active border styles on playlist items
  document.querySelectorAll('.playlist-item').forEach(item => {
    item.classList.remove('bg-slate-800/80', 'border-amber-400/50', 'border-brand-red');
    item.classList.add('bg-slate-900/60', 'border-slate-800');
  });

  if (el) {
    el.classList.remove('bg-slate-900/60', 'border-slate-800');
    el.classList.add('bg-slate-800/80', 'border-amber-400/50');
  }
}
