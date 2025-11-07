/**
 * Alpine.js Patterns for ICTServe
 * 
 * Common Alpine.js patterns and utilities for the ICTServe application.
 * Includes WCAG 2.2 AA compliant interactions and performance optimizations.
 * 
 * @version 1.1.0
 * @trace D13-UI/UX-Frontend-Framework
 * @wcag-level AA
 */

// Global Alpine.js patterns
document.addEventListener('alpine:init', () => {
    
    // Modal Pattern with Focus Management
    Alpine.data('modal', (initialOpen = false) => ({
        open: initialOpen,
        
        init() {
            this.$watch('open', (value) => {
                if (value) {
                    this.trapFocus();
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        },
        
        openModal() {
            this.open = true;
            this.$nextTick(() => {
                this.$refs.modalContent?.focus();
            });
        },
        
        closeModal() {
            this.open = false;
        },
        
        trapFocus() {
            const focusableElements = this.$el.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            this.$el.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            lastElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            firstElement.focus();
                            e.preventDefault();
                        }
                    }
                }
                
                if (e.key === 'Escape') {
                    this.closeModal();
                }
            });
        }
    }));
    
    // Dropdown Pattern with Keyboard Navigation
    Alpine.data('dropdown', () => ({
        open: false,
        selectedIndex: -1,
        
        init() {
            this.$refs.trigger.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.openDropdown();
                }
            });
        },
        
        openDropdown() {
            this.open = true;
            this.selectedIndex = -1;
            this.$nextTick(() => {
                this.focusFirstItem();
            });
        },
        
        closeDropdown() {
            this.open = false;
            this.selectedIndex = -1;
            this.$refs.trigger.focus();
        },
        
        navigateItems(direction) {
            const items = this.$refs.menu.querySelectorAll('[role="menuitem"]');
            const maxIndex = items.length - 1;
            
            if (direction === 'down') {
                this.selectedIndex = this.selectedIndex < maxIndex ? this.selectedIndex + 1 : 0;
            } else {
                this.selectedIndex = this.selectedIndex > 0 ? this.selectedIndex - 1 : maxIndex;
            }
            
            items[this.selectedIndex]?.focus();
        },
        
        focusFirstItem() {
            const firstItem = this.$refs.menu.querySelector('[role="menuitem"]');
            firstItem?.focus();
            this.selectedIndex = 0;
        }
    }));
    
    // Form Validation Pattern
    Alpine.data('formValidation', () => ({
        errors: {},
        touched: {},
        
        validateField(field, value, rules) {
            this.touched[field] = true;
            this.errors[field] = [];
            
            if (rules.required && (!value || value.trim() === '')) {
                this.errors[field].push(`${field} is required`);
            }
            
            if (rules.email && value && !this.isValidEmail(value)) {
                this.errors[field].push('Please enter a valid email address');
            }
            
            if (rules.minLength && value && value.length < rules.minLength) {
                this.errors[field].push(`${field} must be at least ${rules.minLength} characters`);
            }
            
            if (rules.maxLength && value && value.length > rules.maxLength) {
                this.errors[field].push(`${field} must not exceed ${rules.maxLength} characters`);
            }
        },
        
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        
        hasError(field) {
            return this.errors[field] && this.errors[field].length > 0;
        },
        
        getError(field) {
            return this.errors[field] ? this.errors[field][0] : '';
        }
    }));
    
    // Notification Toast Pattern
    Alpine.data('toast', () => ({
        notifications: [],
        
        show(message, type = 'info', duration = 5000) {
            const id = Date.now();
            const notification = { id, message, type, visible: true };
            
            this.notifications.push(notification);
            
            // Auto-remove after duration
            setTimeout(() => {
                this.remove(id);
            }, duration);
            
            // Announce to screen readers
            this.announceToScreenReader(message);
        },
        
        remove(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications[index].visible = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300); // Wait for animation
            }
        },
        
        announceToScreenReader(message) {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = message;
            
            document.body.appendChild(announcement);
            
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
        }
    }));
    
    // Accordion Pattern
    Alpine.data('accordion', (allowMultiple = false) => ({
        openItems: [],
        
        toggle(index) {
            if (this.isOpen(index)) {
                this.close(index);
            } else {
                this.open(index);
            }
        },
        
        open(index) {
            if (!allowMultiple) {
                this.openItems = [index];
            } else {
                if (!this.openItems.includes(index)) {
                    this.openItems.push(index);
                }
            }
        },
        
        close(index) {
            this.openItems = this.openItems.filter(item => item !== index);
        },
        
        isOpen(index) {
            return this.openItems.includes(index);
        }
    }));
    
    // Tab Pattern with ARIA
    Alpine.data('tabs', (defaultTab = 0) => ({
        activeTab: defaultTab,
        
        init() {
            // Set initial ARIA attributes
            this.updateAriaAttributes();
        },
        
        setActiveTab(index) {
            this.activeTab = index;
            this.updateAriaAttributes();
            
            // Focus the selected tab
            const tabButton = this.$refs.tablist.children[index];
            tabButton?.focus();
        },
        
        updateAriaAttributes() {
            // Update tab buttons
            const tabButtons = this.$refs.tablist.querySelectorAll('[role="tab"]');
            tabButtons.forEach((button, index) => {
                button.setAttribute('aria-selected', index === this.activeTab);
                button.setAttribute('tabindex', index === this.activeTab ? '0' : '-1');
            });
            
            // Update tab panels
            const tabPanels = this.$el.querySelectorAll('[role="tabpanel"]');
            tabPanels.forEach((panel, index) => {
                panel.hidden = index !== this.activeTab;
            });
        },
        
        handleKeydown(event, index) {
            const tabButtons = this.$refs.tablist.querySelectorAll('[role="tab"]');
            const maxIndex = tabButtons.length - 1;
            
            switch (event.key) {
                case 'ArrowRight':
                    event.preventDefault();
                    this.setActiveTab(index < maxIndex ? index + 1 : 0);
                    break;
                case 'ArrowLeft':
                    event.preventDefault();
                    this.setActiveTab(index > 0 ? index - 1 : maxIndex);
                    break;
                case 'Home':
                    event.preventDefault();
                    this.setActiveTab(0);
                    break;
                case 'End':
                    event.preventDefault();
                    this.setActiveTab(maxIndex);
                    break;
            }
        }
    }));
    
    // Loading State Pattern
    Alpine.data('loadingState', () => ({
        loading: false,
        
        async withLoading(asyncFunction) {
            this.loading = true;
            try {
                await asyncFunction();
            } finally {
                this.loading = false;
            }
        }
    }));
    
    // Search with Debounce Pattern
    Alpine.data('search', (delay = 300) => ({
        query: '',
        results: [],
        loading: false,
        debounceTimer: null,
        
        init() {
            this.$watch('query', () => {
                this.debouncedSearch();
            });
        },
        
        debouncedSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.performSearch();
            }, delay);
        },
        
        async performSearch() {
            if (!this.query.trim()) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            try {
                // Implement your search logic here
                // This is a placeholder
                this.results = await this.searchFunction(this.query);
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
        
        async searchFunction(query) {
            // Override this method with actual search implementation
            return [];
        }
    }));
});

// Utility functions
window.AlpineUtils = {
    // Announce message to screen readers
    announceToScreenReader(message, priority = 'polite') {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', priority);
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    },
    
    // Focus management utilities
    trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        element.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    },
    
    // Debounce utility
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};