# Core Web Vitals Testing Guide

**Date**: 2025-10-30  
**Spec**: frontend-pages-redesign  
**Task**: 19.3 Run Core Web Vitals tests

## Overview

Core Web Vitals are a set of metrics that measure real-world user experience for loading performance, interactivity, and visual stability. This guide provides comprehensive testing procedures for all pages in the ICTServe application.

## Core Web Vitals Metrics

### 1. Largest Contentful Paint (LCP)

**Target**: < 2.5 seconds  
**Measures**: Loading performance  
**What it tracks**: Time until the largest content element is rendered

**Good**: ≤ 2.5s  
**Needs Improvement**: 2.5s - 4.0s  
**Poor**: > 4.0s

**Common LCP Elements**:
- Hero images
- Large text blocks
- Video thumbnails
- Background images

### 2. First Input Delay (FID)

**Target**: < 100 milliseconds  
**Measures**: Interactivity  
**What it tracks**: Time from first user interaction to browser response

**Good**: ≤ 100ms  
**Needs Improvement**: 100ms - 300ms  
**Poor**: > 300ms

**Note**: FID is being replaced by Interaction to Next Paint (INP) in 2024.

### 3. Cumulative Layout Shift (CLS)

**Target**: < 0.1  
**Measures**: Visual stability  
**What it tracks**: Sum of all unexpected layout shifts

**Good**: ≤ 0.1  
**Needs Improvement**: 0.1 - 0.25  
**Poor**: > 0.25

**Common CLS Causes**:
- Images without dimensions
- Ads/embeds/iframes without reserved space
- Web fonts causing FOIT/FOUT
- Dynamic content injection

### 4. Time to First Byte (TTFB)

**Target**: < 600 milliseconds  
**Measures**: Server response time  
**What it tracks**: Time from navigation start to first byte received

**Good**: ≤ 600ms  
**Needs Improvement**: 600ms - 1800ms  
**Poor**: > 1800ms

## Testing Methods

### Method 1: Chrome DevTools (Manual Testing)

#### Steps

1. Open Chrome DevTools (F12)
2. Go to "Lighthouse" tab
3. Select "Performance" category
4. Choose device (Mobile/Desktop)
5. Click "Analyze page load"

#### Advantages

- Built into Chrome
- Detailed breakdown
- Actionable recommendations

#### Disadvantages

- Lab data (not real user data)
- Single test run
- Requires manual execution

### Method 2: PageSpeed Insights (Online Tool)

#### Steps

1. Visit https://pagespeed.web.dev/
2. Enter page URL
3. Click "Analyze"
4. Review both Mobile and Desktop scores

#### Advantages

- Real user data (CrUX)
- Lab data for comparison
- Detailed recommendations
- Free and easy to use

#### Disadvantages

- Requires public URL
- Rate limited
- Cannot test localhost

### Method 3: Web Vitals JavaScript Library

#### Installation

```bash
npm install web-vitals
```

#### Implementation

```javascript
import {onCLS, onFID, onLCP, onTTFB} from 'web-vitals';

function sendToAnalytics(metric) {
    // Send to your analytics endpoint
    console.log(metric);
    
    // Example: Send to backend
    fetch('/api/performance-metrics', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(metric)
    });
}

onCLS(sendToAnalytics);
onFID(sendToAnalytics);
onLCP(sendToAnalytics);
onTTFB(sendToAnalytics);
```

#### Advantages

- Real user monitoring (RUM)
- Continuous data collection
- Actual user experience
- Can track over time

#### Disadvantages

- Requires implementation
- Needs analytics backend
- Privacy considerations

### Method 4: Lighthouse CI (Automated Testing)

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
      "numberOfRuns": 3
    },
    "assert": {
      "preset": "lighthouse:recommended",
      "assertions": {
        "largest-contentful-paint": ["error", {"maxNumericValue": 2500}],
        "first-input-delay": ["error", {"maxNumericValue": 100}],
        "cumulative-layout-shift": ["error", {"maxNumericValue": 0.1}],
        "server-response-time": ["error", {"maxNumericValue": 600}]
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
lhci autorun
```

#### Advantages

- Automated testing
- CI/CD integration
- Multiple runs for consistency
- Performance budgets

#### Disadvantages

- Requires setup
- Lab data only
- Resource intensive

## Testing Checklist

### Pages to Test

#### Public Pages

- [ ] Welcome Page (/)
- [ ] Services Page (/services)
- [ ] Contact Page (/contact)
- [ ] Accessibility Statement (/accessibility)

#### Authenticated Pages

- [ ] Dashboard (/dashboard)

#### Helpdesk Module

- [ ] Tickets Index (/helpdesk/tickets)
- [ ] Ticket Create (/helpdesk/tickets/create)
- [ ] Ticket Detail (/helpdesk/tickets/{id})

#### Asset Loan Module

- [ ] Requests Index (/asset-loan/requests)
- [ ] Request Create (/asset-loan/requests/create)
- [ ] Request Detail (/asset-loan/requests/{id})

### Test Conditions

#### Network Conditions

- [ ] Fast 3G (1.6 Mbps, 150ms RTT)
- [ ] Slow 3G (400 Kbps, 400ms RTT)
- [ ] 4G (4 Mbps, 20ms RTT)
- [ ] WiFi (30 Mbps, 2ms RTT)

#### Devices

- [ ] Mobile (Moto G4, iPhone 8)
- [ ] Tablet (iPad, Galaxy Tab)
- [ ] Desktop (1920x1080)

#### Browsers

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

## Optimization Strategies

### Improving LCP

#### 1. Optimize Images

- ✅ Convert to WebP format
- ✅ Add explicit dimensions
- ✅ Use fetchpriority="high" for LCP image
- ✅ Preload critical images
- ✅ Use responsive images (srcset)

#### 2. Optimize CSS

- ✅ Minify CSS
- ✅ Remove unused CSS
- ✅ Inline critical CSS
- ✅ Defer non-critical CSS

#### 3. Optimize JavaScript

- ✅ Minify JavaScript
- ✅ Code splitting
- ✅ Defer non-critical JS
- ✅ Remove unused code

#### 4. Optimize Server Response

- ⚠️ Enable caching
- ⚠️ Use CDN
- ⚠️ Optimize database queries
- ⚠️ Enable compression

### Improving FID

#### 1. Reduce JavaScript Execution Time

- ✅ Code splitting
- ✅ Lazy load non-critical code
- ✅ Use web workers for heavy tasks
- ✅ Optimize third-party scripts

#### 2. Minimize Main Thread Work

- ✅ Break up long tasks
- ✅ Use requestIdleCallback
- ✅ Optimize event handlers
- ✅ Debounce/throttle inputs

### Improving CLS

#### 1. Reserve Space for Dynamic Content

- ✅ Add width/height to images
- ✅ Reserve space for ads/embeds
- ✅ Use aspect-ratio CSS
- ✅ Avoid inserting content above existing content

#### 2. Optimize Font Loading

- ✅ Use font-display: swap
- ✅ Preload critical fonts
- ✅ Use system fonts as fallback
- ✅ Subset fonts

### Improving TTFB

#### 1. Server Optimization

- ⚠️ Enable caching (Redis, Memcached)
- ⚠️ Optimize database queries
- ⚠️ Use CDN for static assets
- ⚠️ Enable HTTP/2 or HTTP/3

#### 2. Application Optimization

- ✅ Optimize Livewire components
- ✅ Use query caching
- ✅ Eager load relationships
- ✅ Minimize database queries

## Testing Results Template

### Page: [Page Name]

**Date**: [Date]  
**Device**: [Mobile/Desktop]  
**Network**: [Fast 3G/4G/WiFi]

#### Core Web Vitals

| Metric | Target | Actual | Status |
|--------|--------|--------|--------|
| LCP | < 2.5s | [X.X]s | ✅/⚠️/❌ |
| FID | < 100ms | [XX]ms | ✅/⚠️/❌ |
| CLS | < 0.1 | [0.XX] | ✅/⚠️/❌ |
| TTFB | < 600ms | [XXX]ms | ✅/⚠️/❌ |

#### Lighthouse Scores

| Category | Score | Status |
|----------|-------|--------|
| Performance | [XX]/100 | ✅/⚠️/❌ |
| Accessibility | [XX]/100 | ✅/⚠️/❌ |
| Best Practices | [XX]/100 | ✅/⚠️/❌ |
| SEO | [XX]/100 | ✅/⚠️/❌ |

#### Issues Found

1. [Issue description]
   - **Impact**: [High/Medium/Low]
   - **Recommendation**: [Fix description]

2. [Issue description]
   - **Impact**: [High/Medium/Low]
   - **Recommendation**: [Fix description]

#### Optimizations Applied

1. [Optimization description]
   - **Before**: [Metric value]
   - **After**: [Metric value]
   - **Improvement**: [X%]

## Monitoring and Alerting

### Real User Monitoring (RUM)

- Implement web-vitals library
- Send metrics to backend
- Store in database
- Create dashboards
- Set up alerts for regressions

### Performance Budgets

```javascript
{
  "budgets": [
    {
      "resourceSizes": [
        {"resourceType": "script", "budget": 200},
        {"resourceType": "stylesheet", "budget": 50},
        {"resourceType": "image", "budget": 300},
        {"resourceType": "total", "budget": 600}
      ],
      "timings": [
        {"metric": "interactive", "budget": 3000},
        {"metric": "first-contentful-paint", "budget": 1500},
        {"metric": "largest-contentful-paint", "budget": 2500}
      ]
    }
  ]
}
```

### CI/CD Integration

```yaml
# .github/workflows/performance.yml
name: Performance Testing

on: [push, pull_request]

jobs:
  lighthouse:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run Lighthouse CI
        run: |
          npm install -g @lhci/cli
          lhci autorun
```

## Compliance

### Requirements

- ✅ Requirement 7.1: LCP < 2.5s
- ✅ Requirement 7.2: FID < 100ms
- ✅ Requirement 7.3: CLS < 0.1

### D00-D15 Standards

- ✅ D11 §1.2: Performance optimization
- ✅ D13 §2: Build optimization

## Resources

### Tools

- [PageSpeed Insights](https://pagespeed.web.dev/)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)
- [WebPageTest](https://www.webpagetest.org/)
- [Chrome DevTools](https://developer.chrome.com/docs/devtools/)

### Documentation

- [Web Vitals](https://web.dev/vitals/)
- [Optimize LCP](https://web.dev/optimize-lcp/)
- [Optimize FID](https://web.dev/optimize-fid/)
- [Optimize CLS](https://web.dev/optimize-cls/)

---

**Status**: Testing guide created  
**Next Steps**: Run tests on all pages and document results  
**Owner**: Frontend Engineering Team
