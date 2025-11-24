/**
 * Performance Optimizations for ICTServe
 * 
 * Core Web Vitals optimization, lazy loading, and performance monitoring.
 * Targets: LCP < 2.5s, FID < 100ms, CLS < 0.1
 * 
 * @version 1.1.0
 * @trace D11-Technical-Design
 * @performance-target 90+ Lighthouse Score
 */

// Performance monitoring and optimization utilities
class PerformanceOptimizer {
    constructor() {
        this.observer = null;
        // Core Web Vitals metrics tracking
        this.metrics = {
            lcp: null,  // Largest Contentful Paint
            fid: null,  // First Input Delay
            cls: null   // Cumulative Layout Shift
        };
        
        this.init();
    }
    
    init() {
        // Initialize performance monitoring
        this.initCoreWebVitals();
        this.initLazyLoading();
        this.initResourceHints();
        this.initCriticalResourcePrioritization();
        
        // DOM ready optimizations
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.onDOMReady();
            });
        } else {
            this.onDOMReady();
        }
    }
    
    // Core Web Vitals monitoring
    initCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        if ('PerformanceObserver' in window) {
            const lcpObserver = new PerformanceObserver((entryList) => {
                const entries = entryList.getEntries();
                const lastEntry = entries[entries.length - 1];
                this.metrics.lcp = lastEntry.startTime;
                
                // Report if LCP is poor (> 2.5s)
                if (lastEntry.startTime > 2500) {
                    console.warn(`Poor LCP: ${lastEntry.startTime}ms`);
                }
            });
            
            try {
                lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
            } catch (e) {
                console.warn('LCP observation not supported');
            }
            
            // First Input Delay (FID)
            const fidObserver = new PerformanceObserver((entryList) => {
                const entries = entryList.getEntries();
                entries.forEach((entry) => {
                    this.metrics.fid = entry.processingStart - entry.startTime;
                    
                    // Report if FID is poor (> 100ms)
                    if (this.metrics.fid > 100) {
                        console.warn(`Poor FID: ${this.metrics.fid}ms`);
                    }
                });
            });
            
            try {
                fidObserver.observe({ entryTypes: ['first-input'] });
            } catch (e) {
                console.warn('FID observation not supported');
            }
            
            // Cumulative Layout Shift (CLS)
            let clsValue = 0;
            const clsObserver = new PerformanceObserver((entryList) => {
                entryList.getEntries().forEach((entry) => {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                });
                
                this.metrics.cls = clsValue;
                
                // Report if CLS is poor (> 0.1)
                if (clsValue > 0.1) {
                    console.warn(`Poor CLS: ${clsValue}`);
                }
            });
            
            try {
                clsObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (e) {
                console.warn('CLS observation not supported');
            }
        }
    }
    
    // Lazy loading implementation
    initLazyLoading() {
        // Image lazy loading
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Load the image
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    // Load srcset if available
                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-srcset');
                    }
                    
                    // Remove loading class and add loaded class
                    img.classList.remove('lazy-loading');
                    img.classList.add('lazy-loaded');
                    
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px', // Start loading 50px before entering viewport
            threshold: 0.01
        });
        
        // Observe all lazy images
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach((img) => {
            img.classList.add('lazy-loading');
            imageObserver.observe(img);
        });
        
        // Component lazy loading
        const componentObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const component = entry.target;
                    
                    try {
                        // Trigger Livewire lazy loading
                        if (component.hasAttribute('wire:lazy')) {
                            // Livewire will handle this automatically
                            componentObserver.unobserve(component);
                        }
                        
                        // Custom component loading
                        if (component.dataset.lazyComponent) {
                            this.loadComponent(component);
                            componentObserver.unobserve(component);
                        }
                    } catch (error) {
                        console.error('[Performance] Component loading failed:', { error: error.message, component: component.dataset.lazyComponent });
                    }
                }
            });
        }, {
            rootMargin: '100px 0px',
            threshold: 0.01
        });
        
        // Observe lazy components
        const lazyComponents = document.querySelectorAll('[data-lazy-component]');
        lazyComponents.forEach((component) => {
            componentObserver.observe(component);
        });
    }
    
    // Resource hints for critical resources
    initResourceHints() {
        // Preload critical fonts
        const criticalFonts = [
            '/fonts/figtree-regular.woff2',
            '/fonts/figtree-semibold.woff2'
        ];
        
        criticalFonts.forEach((font) => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = font;
            link.as = 'font';
            link.type = 'font/woff2';
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        });
        
        // DNS prefetch for external domains
        const externalDomains = [
            '//fonts.googleapis.com',
            '//fonts.gstatic.com'
        ];
        
        externalDomains.forEach((domain) => {
            const link = document.createElement('link');
            link.rel = 'dns-prefetch';
            link.href = domain;
            document.head.appendChild(link);
        });
    }
    
    // Critical resource prioritization
    initCriticalResourcePrioritization() {
        // Cache all images to avoid repeated DOM queries
        const allImages = document.querySelectorAll('img');
        
        allImages.forEach((img) => {
            if (img.dataset.priority === 'high') {
                // Prioritize above-the-fold images
                img.loading = 'eager';
                img.fetchPriority = 'high';
            } else if (!img.loading) {
                // Deprioritize below-the-fold resources
                img.loading = 'lazy';
            }
        });
    }
    
    // DOM ready optimizations
    onDOMReady() {
        // Optimize form interactions
        this.optimizeForms();
        
        // Optimize animations
        this.optimizeAnimations();
        
        // Initialize service worker
        this.initServiceWorker();
        
        // Preload critical routes
        this.preloadCriticalRoutes();
    }
    
    // Form optimization
    optimizeForms() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach((form) => {
            // Cache DOM queries for performance - avoid repeated queries
            const formElements = this.cacheFormElements(form);
            
            formElements.inputs.forEach((input) => {
                let validationTimeout;
                
                input.addEventListener('input', () => {
                    clearTimeout(validationTimeout);
                    validationTimeout = setTimeout(() => {
                        // Trigger Livewire validation
                        if (input.hasAttribute('wire:model')) {
                            // Livewire will handle this
                        }
                    }, 300);
                });
            });
            
            // Optimize form submission
            form.addEventListener('submit', (e) => {
                if (formElements.submitButton) {
                    formElements.submitButton.disabled = true;
                    formElements.submitButton.setAttribute('aria-busy', 'true');
                    
                    // Re-enable after 5 seconds as fallback
                    setTimeout(() => {
                        formElements.submitButton.disabled = false;
                        formElements.submitButton.removeAttribute('aria-busy');
                    }, 5000);
                }
            });
        });
    }
    
    // Cache form elements to avoid repeated DOM queries
    cacheFormElements(form) {
        return {
            inputs: form.querySelectorAll('input, textarea, select'),
            submitButton: form.querySelector('button[type="submit"]')
        };
    }
    
    // Animation optimization
    optimizeAnimations() {
        // Respect user's motion preferences
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        if (prefersReducedMotion.matches) {
            // Disable animations for users who prefer reduced motion
            document.documentElement.style.setProperty('--animation-duration', '0.01ms');
            document.documentElement.style.setProperty('--transition-duration', '0.01ms');
        }
        
        // Use CSS containment for animated elements - cache query
        const animatedElements = document.querySelectorAll('[class*="animate-"], [class*="transition-"]');
        animatedElements.forEach((element) => {
            element.style.contain = 'layout style paint';
        });
    }
    
    // Service worker initialization
    initServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    }
    
    // Preload critical routes
    preloadCriticalRoutes() {
        const criticalRoutes = [
            '/helpdesk',
            '/loan',
            '/staff/dashboard'
        ];
        
        // Use requestIdleCallback for non-critical preloading
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                criticalRoutes.forEach((route) => {
                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = route;
                    document.head.appendChild(link);
                });
            });
        }
    }
    
    // Load component dynamically
    async loadComponent(element) {
        const componentName = element.dataset.lazyComponent;
        
        try {
            // Show loading state
            element.textContent = 'Loading...';
            element.className = 'animate-pulse bg-gray-200 h-20 rounded';
            
            // Simulate component loading (replace with actual implementation)
            await new Promise(resolve => setTimeout(resolve, 100));
            
            // Load component content - sanitize component name
            const sanitizedName = String(componentName).replace(/<[^>]*>/g, '');
            element.textContent = `Loaded ${sanitizedName}`;
            element.classList.add('component-loaded');
            
        } catch (error) {
            console.error('[Performance] Component load failed:', { error: error.message, component: componentName });
            element.textContent = 'Failed to load component';
            element.className = 'text-red-500';
        }
    }
    
    // Get performance metrics
    getMetrics() {
        return { ...this.metrics };
    }
    
    // Report performance metrics
    reportMetrics() {
        try {
            // Send metrics to analytics service
            if (window.gtag) {
                window.gtag('event', 'web_vitals', {
                    lcp: this.metrics.lcp,
                    fid: this.metrics.fid,
                    cls: this.metrics.cls
                });
            }
            
            console.log('Performance Metrics:', this.getMetrics());
        } catch (error) {
            console.error('[Performance] Metrics reporting failed:', { error: error.message });
        }
    }
}

// Initialize performance optimizer
const performanceOptimizer = new PerformanceOptimizer();

// Report metrics after page load
window.addEventListener('load', () => {
    // Wait for metrics collection to stabilize (2 seconds)
    const METRICS_COLLECTION_DELAY = 2000;
    setTimeout(() => {
        performanceOptimizer.reportMetrics();
    }, METRICS_COLLECTION_DELAY);
});

// Export for global access with namespace
if (!window.ICTServe) {
    window.ICTServe = {};
}
window.ICTServe.PerformanceOptimizer = performanceOptimizer;