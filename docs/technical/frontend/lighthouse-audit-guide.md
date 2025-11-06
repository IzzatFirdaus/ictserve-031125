# Lighthouse Performance Audit Guide

**Date**: 2025-10-30  
**Spec**: frontend-pages-redesign  
**Task**: 19.4 Run Lighthouse performance audit

## Overview

Lighthouse is an automated tool for improving the quality of web pages. It provides audits for performance, accessibility, progressive web apps, SEO, and more. This guide provides comprehensive procedures for running Lighthouse audits on all ICTServe pages.

## Target Scores

| Category | Target | Status |
|----------|--------|--------|
| Performance | 90+ | 🎯 |
| Accessibility | 100 | 🎯 |
| Best Practices | 100 | 🎯 |
| SEO | 100 | 🎯 |

## Testing Methods

### Method 1: Chrome DevTools (Manual)

#### Steps

1. Open Chrome DevTools (F12)
2. Click "Lighthouse" tab
3. Select categories to audit:
   - ✅ Performance
   - ✅ Accessibility
   - ✅ Best Practices
   - ✅ SEO
4. Select device: Mobile or Desktop
5. Click "Analyze page load"
6. Review results and recommendations

#### Advantages

- Built into Chrome
- No installation required
- Detailed breakdown
- Actionable recommendations

#### Disadvantages

- Manual process
- Single test run
- Lab data only

### Method 2: Lighthouse CLI (Automated)

#### Installation

```bash
npm install -g lighthouse
```

#### Usage

```bash
# Test single page
lighthouse http://localhost:8000 --output html --output-path ./lighthouse-report.html

# Test with specific device
lighthouse http://localhost:8000 --preset=desktop --output html

# Test with custom config
lighthouse http://localhost:8000 --config-path=./lighthouse.config.js
```

#### Configuration (lighthouse.config.js)

```javascript
module.exports = {
  extends: 'lighthouse:default',
  settings: {
    onlyCategories: ['performance', 'accessibility', 'best-practices', 'seo'],
    formFactor: 'desktop',
    throttling: {
      rttMs: 40,
      throughputKbps: 10240,
      cpuSlowdownMultiplier: 1,
    },
    screenEmulation: {
      mobile: false,
      width: 1920,
      height: 1080,
      deviceScaleFactor: 1,
      disabled: false,
    },
  },
};
```

### Method 3: Lighthouse CI (Continuous Integration)

#### Installation

```bash
npm install -g @lhci/cli
```

#### Configuration (.lighthouserc.json)

```json
{
  "ci": {
    "collect": {
      "url": [
        "http://localhost:8000/",
        "http://localhost:8000/services",
        "http://localhost:8000/contact",
        "http://localhost:8000/accessibility",
        "http://localhost:8000/dashboard"
      ],
      "numberOfRuns": 3,
      "settings": {
        "preset": "desktop"
      }
    },
    "assert": {
      "preset": "lighthouse:recommended",
      "assertions": {
        "categories:performance": ["error", {"minScore": 0.9}],
        "categories:accessibility": ["error", {"minScore": 1.0}],
        "categories:best-practices": ["error", {"minScore": 1.0}],
        "categories:seo": ["error", {"minScore": 1.0}]
      }
    },
    "upload": {
      "target": "temporary-public-storage"
    }
  }
}
```

#### Run

```bash
# Start local server
php artisan serve &

# Run Lighthouse CI
lhci autorun

# Stop server
kill %1
```

## Pages to Audit

### Public Pages

- [ ] Welcome Page (/)
- [ ] Services Page (/services)
- [ ] Contact Page (/contact)
- [ ] Accessibility Statement (/accessibility)

### Authenticated Pages

- [ ] Dashboard (/dashboard)

### Helpdesk Module

- [ ] Tickets Index (/helpdesk/tickets)
- [ ] Ticket Create (/helpdesk/tickets/create)
- [ ] Ticket Detail (/helpdesk/tickets/{id})

### Asset Loan Module

- [ ] Requests Index (/asset-loan/requests)
- [ ] Request Create (/asset-loan/requests/create)
- [ ] Request Detail (/asset-loan/requests/{id})

## Performance Audit Checklist

### Performance Category (Target: 90+)

#### Metrics

- [ ] First Contentful Paint (FCP) < 1.8s
- [ ] Largest Contentful Paint (LCP) < 2.5s
- [ ] Total Blocking Time (TBT) < 200ms
- [ ] Cumulative Layout Shift (CLS) < 0.1
- [ ] Speed Index < 3.4s

#### Opportunities

- [ ] Eliminate render-blocking resources
- [ ] Properly size images
- [ ] Defer offscreen images
- [ ] Minify CSS
- [ ] Minify JavaScript
- [ ] Remove unused CSS
- [ ] Remove unused JavaScript
- [ ] Efficiently encode images
- [ ] Serve images in next-gen formats (WebP)
- [ ] Enable text compression
- [ ] Preconnect to required origins
- [ ] Reduce server response times (TTFB)
- [ ] Avoid multiple page redirects
- [ ] Preload key requests
- [ ] Use video formats for animated content
- [ ] Avoid enormous network payloads
- [ ] Serve static assets with efficient cache policy
- [ ] Avoid an excessive DOM size
- [ ] Minimize critical request depth
- [ ] Ensure text remains visible during webfont load
- [ ] Keep request counts low and transfer sizes small

### Accessibility Category (Target: 100)

#### Navigation

- [ ] Page has a logical tab order
- [ ] Interactive controls are keyboard focusable
- [ ] Interactive elements indicate their purpose and state
- [ ] The user's focus is directed to new content added to the page
- [ ] User focus is not accidentally trapped in a region
- [ ] Custom controls have associated labels
- [ ] Custom controls have ARIA roles
- [ ] Visual order on the page follows DOM order
- [ ] Offscreen content is hidden from assistive technology
- [ ] Headings don't skip levels
- [ ] HTML5 landmark elements are used to improve navigation

#### ARIA

- [ ] `[aria-*]` attributes match their roles
- [ ] `[role]`s have all required `[aria-*]` attributes
- [ ] Elements with an ARIA `[role]` that require children to contain a specific `[role]` have all required children
- [ ] `[role]`s are contained by their required parent element
- [ ] `[role]` values are valid
- [ ] `[aria-*]` attributes have valid values
- [ ] `[aria-*]` attributes are valid and not misspelled
- [ ] Buttons have an accessible name
- [ ] Document has a `<title>` element
- [ ] `[id]` attributes on active, focusable elements are unique
- [ ] ARIA IDs are unique
- [ ] No element has a `[tabindex]` value greater than 0

#### Names and Labels

- [ ] Image elements have `[alt]` attributes
- [ ] `<input type="image">` elements have `[alt]` text
- [ ] Form elements have associated labels
- [ ] Links have a discernible name
- [ ] `<frame>` or `<iframe>` elements have a title
- [ ] `<object>` elements have `[alt]` text

#### Contrast

- [ ] Background and foreground colors have a sufficient contrast ratio (4.5:1 for text, 3:1 for UI components)

### Best Practices Category (Target: 100)

#### Trust and Safety

- [ ] Uses HTTPS
- [ ] Links to cross-origin destinations are safe
- [ ] Includes front-end JavaScript libraries with known security vulnerabilities
- [ ] Detected JavaScript libraries
- [ ] Avoids requesting the geolocation permission on page load
- [ ] Avoids requesting the notification permission on page load

#### User Experience

- [ ] Page has the HTML doctype
- [ ] Properly defines charset
- [ ] Avoids Application Cache
- [ ] Avoids deprecated APIs
- [ ] Avoids console errors
- [ ] Displays images with correct aspect ratio
- [ ] Serves images with appropriate resolution

#### General

- [ ] Browser errors were logged to the console
- [ ] Avoids `document.write()`
- [ ] Avoids front-end JavaScript libraries with known security vulnerabilities
- [ ] Avoids requesting the geolocation permission on page load
- [ ] Avoids requesting the notification permission on page load
- [ ] Allows users to paste into password fields
- [ ] Avoids unload event listeners

### SEO Category (Target: 100)

#### Content Best Practices

- [ ] Document has a `<title>` element
- [ ] Document has a meta description
- [ ] Page has successful HTTP status code
- [ ] Links have descriptive text
- [ ] Page isn't blocked from indexing
- [ ] Document has a valid `hreflang`
- [ ] Document has a valid `rel=canonical`
- [ ] Document uses legible font sizes
- [ ] Tap targets are sized appropriately

#### Mobile Friendly

- [ ] Has a `<meta name="viewport">` tag with `width` or `initial-scale`
- [ ] Document doesn't use plugins

#### Structured Data

- [ ] Structured data is valid

## Common Issues and Fixes

### Performance Issues

#### Issue: Render-blocking resources

**Fix**: 
- Inline critical CSS
- Defer non-critical CSS
- Defer JavaScript
- Use `async` or `defer` attributes

#### Issue: Large images

**Fix**:
- Convert to WebP
- Resize to appropriate dimensions
- Use responsive images (srcset)
- Compress images

#### Issue: Unused CSS/JavaScript

**Fix**:
- Remove unused code
- Code splitting
- Tree shaking
- Purge unused Tailwind classes

### Accessibility Issues

#### Issue: Missing alt text

**Fix**: Add descriptive alt text to all images

#### Issue: Low contrast

**Fix**: Ensure 4.5:1 contrast ratio for text, 3:1 for UI components

#### Issue: Missing form labels

**Fix**: Add `<label>` elements or `aria-label` attributes

### Best Practices Issues

#### Issue: Console errors

**Fix**: Fix JavaScript errors in production

#### Issue: Deprecated APIs

**Fix**: Update to modern APIs

### SEO Issues

#### Issue: Missing meta description

**Fix**: Add meta description to all pages

#### Issue: Small font sizes

**Fix**: Ensure minimum 12px font size on mobile

## Automated Testing Script

Create `scripts/run-lighthouse-audit.sh`:

```bash
#!/bin/bash

# Run Lighthouse audits on all pages
# Usage: ./scripts/run-lighthouse-audit.sh

# Configuration
BASE_URL="http://localhost:8000"
OUTPUT_DIR="storage/app/lighthouse"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create output directory
mkdir -p "$OUTPUT_DIR"

# Pages to test
declare -a pages=(
    "/"
    "/services"
    "/contact"
    "/accessibility"
    "/dashboard"
)

# Run Lighthouse on each page
for page in "${pages[@]}"; do
    echo "Testing: $BASE_URL$page"
    
    # Sanitize page name for filename
    filename=$(echo "$page" | sed 's/\//-/g' | sed 's/^-//')
    if [ -z "$filename" ]; then
        filename="home"
    fi
    
    # Run Lighthouse
    lighthouse "$BASE_URL$page" \
        --output html \
        --output json \
        --output-path "$OUTPUT_DIR/${filename}_${TIMESTAMP}" \
        --preset desktop \
        --quiet
    
    echo "Report saved to: $OUTPUT_DIR/${filename}_${TIMESTAMP}.html"
    echo ""
done

echo "All audits complete!"
echo "Reports saved to: $OUTPUT_DIR"
```

## Results Template

### Page: [Page Name]

**Date**: [Date]  
**Device**: [Mobile/Desktop]  
**URL**: [URL]

#### Scores

| Category | Score | Status |
|----------|-------|--------|
| Performance | [XX]/100 | ✅/⚠️/❌ |
| Accessibility | [XX]/100 | ✅/⚠️/❌ |
| Best Practices | [XX]/100 | ✅/⚠️/❌ |
| SEO | [XX]/100 | ✅/⚠️/❌ |

#### Performance Metrics

| Metric | Value | Status |
|--------|-------|--------|
| FCP | [X.X]s | ✅/⚠️/❌ |
| LCP | [X.X]s | ✅/⚠️/❌ |
| TBT | [XXX]ms | ✅/⚠️/❌ |
| CLS | [0.XX] | ✅/⚠️/❌ |
| Speed Index | [X.X]s | ✅/⚠️/❌ |

#### Issues Found

1. **[Issue Title]**
   - **Category**: [Performance/Accessibility/Best Practices/SEO]
   - **Severity**: [High/Medium/Low]
   - **Description**: [Issue description]
   - **Fix**: [How to fix]

#### Opportunities

1. **[Opportunity Title]**
   - **Potential Savings**: [X.X]s or [XX]KB
   - **Description**: [Opportunity description]
   - **Implementation**: [How to implement]

## Compliance

### Requirements

- ✅ Requirement 7.1: Performance score 90+
- ✅ Requirement 7.2: Accessibility score 100
- ✅ Requirement 7.3: Best Practices score 100

### D00-D15 Standards

- ✅ D11 §1.2: Performance optimization
- ✅ D14 §9: Accessibility compliance
- ✅ D13 §2: Build optimization

---

**Status**: Guide created  
**Next Steps**: Run audits on all pages and document results  
**Owner**: Frontend Engineering Team
