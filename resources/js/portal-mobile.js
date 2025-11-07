/**
 * name: portal-mobile.js
 * description: Mobile touch interactions and gestures for authenticated portal
 * author: dev-team@motac.gov.my
 * trace: D12 §4.2, D14 §3 (Requirements 11.3, 11.5)
 * last-updated: 2025-11-06
 */

/**
 * Initialize mobile touch interactions
 */
document.addEventListener('DOMContentLoaded', () => {
    initializeFAB();
    initializeSwipeGestures();
    initializePullToRefresh();
    initializeResponsiveTables();
    initializeTouchFeedback();
});

/**
 * Floating Action Button (FAB) functionality
 */
function initializeFAB() {
    const fabButton = document.querySelector('[data-fab-button]');
    const fabMenu = document.querySelector('[data-fab-menu]');

    if (!fabButton || !fabMenu) return;

    let isMenuOpen = false;

    fabButton.addEventListener('click', (e) => {
        e.stopPropagation();
        isMenuOpen = !isMenuOpen;

        if (isMenuOpen) {
            fabMenu.classList.remove('hidden');
            fabMenu.classList.add('animate-fade-in-up');
            fabButton.querySelector('svg').classList.add('rotate-45');
        } else {
            fabMenu.classList.add('hidden');
            fabButton.querySelector('svg').classList.remove('rotate-45');
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (isMenuOpen && !fabButton.contains(e.target) && !fabMenu.contains(e.target)) {
            fabMenu.classList.add('hidden');
            fabButton.querySelector('svg').classList.remove('rotate-45');
            isMenuOpen = false;
        }
    });
}

/**
 * Swipe gestures for mobile navigation
 */
function initializeSwipeGestures() {
    const swipeableElements = document.querySelectorAll('[data-swipeable]');

    swipeableElements.forEach(element => {
        let startX = 0;
        let startY = 0;
        let currentX = 0;
        let currentY = 0;
        let isSwiping = false;

        element.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isSwiping = true;
        }, { passive: true });

        element.addEventListener('touchmove', (e) => {
            if (!isSwiping) return;

            currentX = e.touches[0].clientX;
            currentY = e.touches[0].clientY;

            const diffX = currentX - startX;
            const diffY = currentY - startY;

            // Determine swipe direction
            if (Math.abs(diffX) > Math.abs(diffY)) {
                // Horizontal swipe
                if (Math.abs(diffX) > 50) {
                    const direction = diffX > 0 ? 'right' : 'left';
                    handleSwipe(element, direction);
                    isSwiping = false;
                }
            } else {
                // Vertical swipe
                if (Math.abs(diffY) > 50) {
                    const direction = diffY > 0 ? 'down' : 'up';
                    handleSwipe(element, direction);
                    isSwiping = false;
                }
            }
        }, { passive: true });

        element.addEventListener('touchend', () => {
            isSwiping = false;
        }, { passive: true });
    });
}

/**
 * Handle swipe actions
 */
function handleSwipe(element, direction) {
    const action = element.dataset.swipeAction;

    switch (action) {
        case 'dismiss':
            if (direction === 'left' || direction === 'right') {
                element.classList.add('animate-slide-out');
                setTimeout(() => {
                    element.dispatchEvent(new CustomEvent('swipe-dismiss'));
                }, 300);
            }
            break;
        case 'navigate':
            if (direction === 'right') {
                window.history.back();
            }
            break;
        case 'refresh':
            if (direction === 'down') {
                element.dispatchEvent(new CustomEvent('swipe-refresh'));
            }
            break;
    }
}

/**
 * Pull-to-refresh functionality
 */
function initializePullToRefresh() {
    const refreshContainer = document.querySelector('[data-pull-to-refresh]');
    if (!refreshContainer) return;

    let startY = 0;
    let currentY = 0;
    let isPulling = false;
    const threshold = 80;

    const spinner = document.createElement('div');
    spinner.className = 'pull-to-refresh-spinner hidden';
    spinner.innerHTML = `
        <svg class="animate-spin h-6 w-6 text-primary-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    refreshContainer.insertBefore(spinner, refreshContainer.firstChild);

    refreshContainer.addEventListener('touchstart', (e) => {
        if (refreshContainer.scrollTop === 0) {
            startY = e.touches[0].clientY;
            isPulling = true;
        }
    }, { passive: true });

    refreshContainer.addEventListener('touchmove', (e) => {
        if (!isPulling) return;

        currentY = e.touches[0].clientY;
        const diff = currentY - startY;

        if (diff > 0 && refreshContainer.scrollTop === 0) {
            e.preventDefault();

            if (diff >= threshold) {
                spinner.classList.remove('hidden');
            }
        }
    });

    refreshContainer.addEventListener('touchend', () => {
        if (!isPulling) return;

        const diff = currentY - startY;

        if (diff >= threshold) {
            // Trigger refresh
            spinner.classList.remove('hidden');
            window.Livewire.dispatch('refresh-dashboard');

            setTimeout(() => {
                spinner.classList.add('hidden');
            }, 1500);
        }

        isPulling = false;
        currentY = 0;
        startY = 0;
    }, { passive: true });
}

/**
 * Convert tables to card layout on mobile
 */
function initializeResponsiveTables() {
    const tables = document.querySelectorAll('[data-responsive-table]');

    tables.forEach(table => {
        if (window.innerWidth < 768) {
            convertTableToCards(table);
        }
    });

    // Re-check on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            tables.forEach(table => {
                if (window.innerWidth < 768) {
                    convertTableToCards(table);
                } else {
                    restoreTableView(table);
                }
            });
        }, 250);
    });
}

/**
 * Convert table to mobile card layout
 */
function convertTableToCards(table) {
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach((cell, index) => {
            const label = document.createElement('div');
            label.className = 'font-medium text-gray-600 dark:text-gray-400 text-xs mb-1';
            label.textContent = headers[index];

            if (!cell.querySelector('.mobile-label')) {
                cell.classList.add('block', 'py-2');
                cell.insertBefore(label, cell.firstChild);
                label.classList.add('mobile-label');
            }
        });

        row.classList.add('flex', 'flex-col', 'bg-white', 'dark:bg-gray-800', 'rounded-lg', 'shadow', 'mb-3', 'p-4');
    });

    table.querySelector('thead').classList.add('hidden');
}

/**
 * Restore table view for desktop
 */
function restoreTableView(table) {
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            const label = cell.querySelector('.mobile-label');
            if (label) {
                label.remove();
            }
            cell.classList.remove('block', 'py-2');
        });

        row.classList.remove('flex', 'flex-col', 'bg-white', 'dark:bg-gray-800', 'rounded-lg', 'shadow', 'mb-3', 'p-4');
    });

    table.querySelector('thead').classList.remove('hidden');
}

/**
 * Touch feedback for interactive elements
 */
function initializeTouchFeedback() {
    const touchableElements = document.querySelectorAll('[data-touchable]');

    touchableElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.classList.add('opacity-70', 'scale-95');
        }, { passive: true });

        element.addEventListener('touchend', function() {
            setTimeout(() => {
                this.classList.remove('opacity-70', 'scale-95');
            }, 150);
        }, { passive: true });

        element.addEventListener('touchcancel', function() {
            this.classList.remove('opacity-70', 'scale-95');
        }, { passive: true });
    });
}

/**
 * Optimize performance for mobile
 */
function optimizeMobilePerformance() {
    // Lazy load images below the fold
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Debounce scroll events
    let scrollTimer;
    window.addEventListener('scroll', () => {
        if (scrollTimer) return;

        scrollTimer = setTimeout(() => {
            document.body.classList.remove('scrolling');
            scrollTimer = null;
        }, 150);

        document.body.classList.add('scrolling');
    }, { passive: true });
}

// Initialize performance optimizations
optimizeMobilePerformance();

// Export functions for use in Livewire components
window.PortalMobile = {
    initializeFAB,
    initializeSwipeGestures,
    initializePullToRefresh,
    initializeResponsiveTables,
    initializeTouchFeedback
};
