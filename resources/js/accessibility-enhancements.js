/**
 * Accessibility Enhancements for ICTServe
 * 
 * WCAG 2.2 AA compliance utilities and enhancements.
 * Includes keyboard navigation, screen reader support, and focus management.
 * 
 * @version 1.1.0
 * @trace D12-UI/UX-Design-Guide
 * @wcag-level AA
 * @compliance WCAG 2.2 AA
 */

class AccessibilityEnhancer {
    constructor() {
        this.focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        this.announcements = [];
        
        this.init();
    }
    
    init() {
        this.initKeyboardNavigation();
        this.initFocusManagement();
        this.initScreenReaderSupport();
        this.initSkipLinks();
        this.initLiveRegions();
        this.initColorContrastMonitoring();
        this.initMotionPreferences();
        
        // DOM ready enhancements
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.onDOMReady();
            });
        } else {
            this.onDOMReady();
        }
    }
    
    // Keyboard navigation enhancements
    initKeyboardNavigation() {
        // Global keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Skip to main content (Alt + M)
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                this.skipToMain();
            }
            
            // Skip to navigation (Alt + N)
            if (e.altKey && e.key === 'n') {
                e.preventDefault();
                this.skipToNavigation();
            }
            
            // Open accessibility menu (Alt + A)
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                this.openAccessibilityMenu();
            }
            
            // Escape key handling
            if (e.key === 'Escape') {
                this.handleEscapeKey();
            }
        });
        
        // Enhance form navigation
        this.enhanceFormNavigation();
        
        // Enhance table navigation
        this.enhanceTableNavigation();
    }
    
    // Focus management
    initFocusManagement() {
        // Track focus for debugging
        if (process.env.NODE_ENV === 'development') {
            document.addEventListener('focusin', (e) => {
                console.log('Focus:', e.target);
            });
        }
        
        // Ensure visible focus indicators
        document.addEventListener('focusin', (e) => {
            const element = e.target;
            
            // Add focus class if element doesn't have visible focus styles
            if (!this.hasVisibleFocus(element)) {
                element.classList.add('focus-visible');
            }
        });
        
        document.addEventListener('focusout', (e) => {
            e.target.classList.remove('focus-visible');
        });
        
        // Focus trap for modals
        this.initFocusTraps();
    }
    
    // Screen reader support
    initScreenReaderSupport() {
        // Create live region for announcements
        this.createLiveRegion();
        
        // Enhance dynamic content
        this.enhanceDynamicContent();
        
        // Add screen reader only content where needed
        this.addScreenReaderContent();
    }
    
    // Skip links
    initSkipLinks() {
        const skipLinks = document.createElement('div');
        skipLinks.className = 'skip-links sr-only-focusable';
        skipLinks.innerHTML = `
            <a href="#main-content" class="skip-link">
                ${this.t('accessibility.skip_to_main')}
            </a>
            <a href="#navigation" class="skip-link">
                ${this.t('accessibility.skip_to_navigation')}
            </a>
            <a href="#search" class="skip-link">
                ${this.t('accessibility.skip_to_search')}
            </a>
        `;
        
        document.body.insertBefore(skipLinks, document.body.firstChild);
    }
    
    // Live regions for dynamic content
    initLiveRegions() {
        // Status messages
        this.createLiveRegion('status', 'polite');
        
        // Alert messages
        this.createLiveRegion('alert', 'assertive');
        
        // Log messages
        this.createLiveRegion('log', 'polite');
    }
    
    // Color contrast monitoring
    initColorContrastMonitoring() {
        if (process.env.NODE_ENV === 'development') {
            // Check contrast ratios in development
            this.checkColorContrast();
        }
    }
    
    // Motion preferences
    initMotionPreferences() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        this.updateMotionPreferences(prefersReducedMotion.matches);
        
        prefersReducedMotion.addEventListener('change', (e) => {
            this.updateMotionPreferences(e.matches);
        });
    }
    
    // DOM ready enhancements
    onDOMReady() {
        this.enhanceButtons();
        this.enhanceLinks();
        this.enhanceImages();
        this.enhanceForms();
        this.enhanceTables();
        this.enhanceHeadings();
        this.addLandmarks();
    }
    
    // Form navigation enhancement
    enhanceFormNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.target.matches('input, textarea, select')) {
                // Enter key in text inputs moves to next field
                if (e.key === 'Enter' && e.target.type !== 'textarea' && e.target.type !== 'submit') {
                    e.preventDefault();
                    this.focusNextFormElement(e.target);
                }
            }
        });
    }
    
    // Table navigation enhancement
    enhanceTableNavigation() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach((table) => {
            // Add keyboard navigation to data tables
            if (table.querySelector('th')) {
                this.addTableKeyboardNavigation(table);
            }
        });
    }
    
    // Focus trap implementation
    initFocusTraps() {
        const modals = document.querySelectorAll('[role="dialog"], .modal');
        
        modals.forEach((modal) => {
            this.addFocusTrap(modal);
        });
    }
    
    // Create live region
    createLiveRegion(type = 'status', politeness = 'polite') {
        const existing = document.getElementById(`live-region-${type}`);
        if (existing) return existing;
        
        const liveRegion = document.createElement('div');
        liveRegion.id = `live-region-${type}`;
        liveRegion.setAttribute('aria-live', politeness);
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        
        document.body.appendChild(liveRegion);
        return liveRegion;
    }
    
    // Announce to screen readers
    announce(message, type = 'status') {
        const liveRegion = document.getElementById(`live-region-${type}`);
        if (liveRegion) {
            // Clear previous message
            liveRegion.textContent = '';
            
            // Add new message after a brief delay
            setTimeout(() => {
                liveRegion.textContent = message;
            }, 100);
            
            // Clear message after announcement
            setTimeout(() => {
                liveRegion.textContent = '';
            }, 5000);
        }
    }
    
    // Skip to main content
    skipToMain() {
        const main = document.getElementById('main-content') || document.querySelector('main');
        if (main) {
            main.focus();
            main.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    // Skip to navigation
    skipToNavigation() {
        const nav = document.getElementById('navigation') || document.querySelector('nav');
        if (nav) {
            const firstLink = nav.querySelector('a, button');
            if (firstLink) {
                firstLink.focus();
            }
        }
    }
    
    // Open accessibility menu
    openAccessibilityMenu() {
        // Implementation for accessibility menu
        this.announce(this.t('accessibility.menu_opened'));
    }
    
    // Handle escape key
    handleEscapeKey() {
        // Close modals
        const openModal = document.querySelector('.modal[aria-hidden="false"], [role="dialog"][aria-hidden="false"]');
        if (openModal) {
            this.closeModal(openModal);
            return;
        }
        
        // Close dropdowns
        const openDropdown = document.querySelector('.dropdown-open, [aria-expanded="true"]');
        if (openDropdown) {
            this.closeDropdown(openDropdown);
            return;
        }
        
        // Clear search
        const searchInput = document.querySelector('input[type="search"]:focus');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    }
    
    // Check if element has visible focus
    hasVisibleFocus(element) {
        const styles = window.getComputedStyle(element);
        const outline = styles.outline;
        const boxShadow = styles.boxShadow;
        
        return outline !== 'none' || boxShadow.includes('rgb');
    }
    
    // Focus next form element
    focusNextFormElement(currentElement) {
        const form = currentElement.closest('form');
        if (!form) return;
        
        const formElements = Array.from(form.querySelectorAll(this.focusableElements));
        const currentIndex = formElements.indexOf(currentElement);
        const nextElement = formElements[currentIndex + 1];
        
        if (nextElement) {
            nextElement.focus();
        }
    }
    
    // Add table keyboard navigation
    addTableKeyboardNavigation(table) {
        const cells = table.querySelectorAll('td, th');
        
        cells.forEach((cell, index) => {
            cell.tabIndex = index === 0 ? 0 : -1;
            
            cell.addEventListener('keydown', (e) => {
                const currentRow = cell.parentElement;
                const currentCellIndex = Array.from(currentRow.children).indexOf(cell);
                const rows = Array.from(table.querySelectorAll('tr'));
                const currentRowIndex = rows.indexOf(currentRow);
                
                let targetCell = null;
                
                switch (e.key) {
                    case 'ArrowRight':
                        targetCell = cell.nextElementSibling;
                        break;
                    case 'ArrowLeft':
                        targetCell = cell.previousElementSibling;
                        break;
                    case 'ArrowDown':
                        const nextRow = rows[currentRowIndex + 1];
                        targetCell = nextRow?.children[currentCellIndex];
                        break;
                    case 'ArrowUp':
                        const prevRow = rows[currentRowIndex - 1];
                        targetCell = prevRow?.children[currentCellIndex];
                        break;
                    case 'Home':
                        targetCell = currentRow.firstElementChild;
                        break;
                    case 'End':
                        targetCell = currentRow.lastElementChild;
                        break;
                }
                
                if (targetCell) {
                    e.preventDefault();
                    // Update tabindex
                    cells.forEach(c => c.tabIndex = -1);
                    targetCell.tabIndex = 0;
                    targetCell.focus();
                }
            });
        });
    }
    
    // Add focus trap to modal
    addFocusTrap(modal) {
        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const focusableElements = modal.querySelectorAll(this.focusableElements);
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }
    
    // Enhance buttons
    enhanceButtons() {
        const buttons = document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])');
        
        buttons.forEach((button) => {
            // Add aria-label if button only contains icon
            if (this.isIconOnlyButton(button)) {
                const iconClass = button.querySelector('[class*="icon"], svg')?.className;
                if (iconClass) {
                    button.setAttribute('aria-label', this.getIconLabel(iconClass));
                }
            }
            
            // Ensure minimum touch target size
            this.ensureMinimumTouchTarget(button);
        });
    }
    
    // Enhance links
    enhanceLinks() {
        const links = document.querySelectorAll('a');
        
        links.forEach((link) => {
            // Add context for links that open in new window
            if (link.target === '_blank') {
                const srText = document.createElement('span');
                srText.className = 'sr-only';
                srText.textContent = ` (${this.t('accessibility.opens_new_window')})`;
                link.appendChild(srText);
            }
            
            // Enhance links with only icons
            if (this.isIconOnlyLink(link)) {
                if (!link.getAttribute('aria-label')) {
                    link.setAttribute('aria-label', this.getLinkContext(link));
                }
            }
        });
    }
    
    // Enhance images
    enhanceImages() {
        const images = document.querySelectorAll('img');
        
        images.forEach((img) => {
            // Add empty alt for decorative images
            if (!img.hasAttribute('alt') && this.isDecorativeImage(img)) {
                img.setAttribute('alt', '');
            }
            
            // Warn about missing alt text in development
            if (process.env.NODE_ENV === 'development' && !img.hasAttribute('alt')) {
                console.warn('Image missing alt text:', img);
            }
        });
    }
    
    // Enhance forms
    enhanceForms() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach((form) => {
            // Associate labels with inputs
            this.associateLabelsWithInputs(form);
            
            // Add required field indicators
            this.addRequiredFieldIndicators(form);
            
            // Enhance error messages
            this.enhanceErrorMessages(form);
        });
    }
    
    // Enhance tables
    enhanceTables() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach((table) => {
            // Add caption if missing
            if (!table.querySelector('caption') && !table.getAttribute('aria-label')) {
                const caption = document.createElement('caption');
                caption.className = 'sr-only';
                caption.textContent = this.t('accessibility.data_table');
                table.insertBefore(caption, table.firstChild);
            }
            
            // Associate headers with data cells
            this.associateTableHeaders(table);
        });
    }
    
    // Enhance headings
    enhanceHeadings() {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        
        // Check heading hierarchy
        if (process.env.NODE_ENV === 'development') {
            this.checkHeadingHierarchy(headings);
        }
    }
    
    // Add landmarks
    addLandmarks() {
        // Add main landmark if missing
        if (!document.querySelector('main, [role="main"]')) {
            const mainContent = document.querySelector('#main-content, .main-content');
            if (mainContent) {
                mainContent.setAttribute('role', 'main');
            }
        }
        
        // Add navigation landmarks
        const navs = document.querySelectorAll('nav:not([aria-label]):not([aria-labelledby])');
        navs.forEach((nav, index) => {
            nav.setAttribute('aria-label', `${this.t('accessibility.navigation')} ${index + 1}`);
        });
    }
    
    // Update motion preferences
    updateMotionPreferences(prefersReduced) {
        document.documentElement.classList.toggle('reduce-motion', prefersReduced);
        
        if (prefersReduced) {
            // Disable animations
            document.documentElement.style.setProperty('--animation-duration', '0.01ms');
            document.documentElement.style.setProperty('--transition-duration', '0.01ms');
        } else {
            // Restore animations
            document.documentElement.style.removeProperty('--animation-duration');
            document.documentElement.style.removeProperty('--transition-duration');
        }
    }
    
    // Utility functions
    isIconOnlyButton(button) {
        const text = button.textContent.trim();
        const hasIcon = button.querySelector('svg, [class*="icon"]');
        return hasIcon && text.length === 0;
    }
    
    isIconOnlyLink(link) {
        const text = link.textContent.trim();
        const hasIcon = link.querySelector('svg, [class*="icon"]');
        return hasIcon && text.length === 0;
    }
    
    isDecorativeImage(img) {
        return img.closest('.decoration, .background') || 
               img.classList.contains('decorative') ||
               img.getAttribute('role') === 'presentation';
    }
    
    ensureMinimumTouchTarget(element) {
        const rect = element.getBoundingClientRect();
        if (rect.width < 44 || rect.height < 44) {
            element.style.minWidth = '44px';
            element.style.minHeight = '44px';
        }
    }
    
    getIconLabel(iconClass) {
        // Map icon classes to labels
        const iconLabels = {
            'search': this.t('accessibility.search'),
            'menu': this.t('accessibility.menu'),
            'close': this.t('accessibility.close'),
            'edit': this.t('accessibility.edit'),
            'delete': this.t('accessibility.delete'),
            'save': this.t('accessibility.save'),
            'cancel': this.t('accessibility.cancel')
        };
        
        for (const [key, label] of Object.entries(iconLabels)) {
            if (iconClass.includes(key)) {
                return label;
            }
        }
        
        return this.t('accessibility.button');
    }
    
    getLinkContext(link) {
        // Try to get context from surrounding text
        const parent = link.parentElement;
        const context = parent?.textContent?.trim() || '';
        return context || this.t('accessibility.link');
    }
    
    // Translation helper
    t(key) {
        // Simple translation helper - replace with actual implementation
        const translations = {
            'accessibility.skip_to_main': 'Skip to main content',
            'accessibility.skip_to_navigation': 'Skip to navigation',
            'accessibility.skip_to_search': 'Skip to search',
            'accessibility.opens_new_window': 'opens in new window',
            'accessibility.data_table': 'Data table',
            'accessibility.navigation': 'Navigation',
            'accessibility.search': 'Search',
            'accessibility.menu': 'Menu',
            'accessibility.close': 'Close',
            'accessibility.edit': 'Edit',
            'accessibility.delete': 'Delete',
            'accessibility.save': 'Save',
            'accessibility.cancel': 'Cancel',
            'accessibility.button': 'Button',
            'accessibility.link': 'Link',
            'accessibility.menu_opened': 'Accessibility menu opened'
        };
        
        return translations[key] || key;
    }
}

// Initialize accessibility enhancer
const accessibilityEnhancer = new AccessibilityEnhancer();

// Export for global access
window.AccessibilityEnhancer = accessibilityEnhancer;