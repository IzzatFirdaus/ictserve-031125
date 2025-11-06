# ICTServe Frontend Development Environment

This document provides comprehensive information about the frontend development environment setup for the ICTServe system, including configuration for Vite, Tailwind CSS, ESLint, Stylelint, Laravel Dusk, and accessibility testing tools.

## 🚀 Quick Start

### Prerequisites

- Node.js 18+ 
- PHP 8.2+
- Composer 2.0+
- Chrome/Chromium browser (for Dusk tests)

### Setup

Run the automated setup script:

```powershell
# Windows PowerShell
.\setup-frontend-dev.ps1

# Or manually:
composer run dev:setup
```

## 🛠️ Development Tools

### Asset Compilation (Vite)

**Configuration**: `vite.config.js`

- **Hot Module Replacement (HMR)**: Enabled for rapid development
- **Source Maps**: Enabled for debugging
- **Code Splitting**: Automatic vendor chunk separation
- **Asset Optimization**: Inline small assets, optimize images
- **CSS Processing**: PostCSS with Tailwind CSS and Autoprefixer

**Commands**:
```bash
npm run dev    # Start development server with HMR
npm run build  # Build optimized production assets
```

### CSS Framework (Tailwind CSS)

**Configuration**: `tailwind.config.js`

- **MOTAC Design Tokens**: Corporate colors, typography, spacing
- **Responsive Design**: Mobile-first breakpoints
- **Dark Mode**: Class-based dark mode support
- **Component Classes**: Utility-first with component extraction
- **Purge CSS**: Automatic unused CSS removal

**MOTAC Design System Colors**:
- Primary: `motac-blue` (#003366)
- Secondary: `motac-yellow` (#FFD700)
- Surface: `motac-surface` (#F7F7F7)
- Status colors: success, warning, error

### Code Quality (ESLint)

**Configuration**: `eslint.config.js`

- **Modern JavaScript**: ES2022+ support
- **Laravel/Livewire Globals**: Alpine.js, Livewire
- **Accessibility Rules**: WCAG compliance checks
- **Code Style**: Consistent formatting rules
- **Error Prevention**: Common mistake detection

**Commands**:
```bash
npm run lint:js        # Lint JavaScript files
npm run lint:js --fix  # Auto-fix linting issues
```

### CSS Linting (Stylelint)

**Configuration**: `.stylelintrc.json`

- **Tailwind CSS Support**: Ignore Tailwind directives
- **CSS Standards**: Standard CSS rules
- **SCSS Support**: Sass/SCSS syntax support
- **Order Rules**: Consistent property ordering
- **Accessibility**: Color contrast validation

**Commands**:
```bash
npm run lint:css        # Lint CSS/SCSS files
npm run lint:css --fix  # Auto-fix CSS issues
```

### Code Formatting (Prettier)

**Configuration**: `.prettierrc`

- **Consistent Style**: Unified code formatting
- **Blade Template Support**: Laravel Blade formatting
- **CSS/SCSS Support**: Stylesheet formatting
- **JavaScript/TypeScript**: Modern JS formatting

**Commands**:
```bash
npm run format  # Format all supported files
```

### PHP Code Style (Laravel Pint)

**Built-in Configuration**: Uses Laravel's default style

- **PSR-12 Compliance**: PHP coding standards
- **Laravel Conventions**: Framework-specific rules
- **Automatic Fixing**: Code style corrections

**Commands**:
```bash
vendor/bin/pint        # Fix code style issues
vendor/bin/pint --test # Check without fixing
```

## 🧪 Testing Environment

### Browser Testing (Laravel Dusk)

**Setup**: Automatically configured with Chrome driver

- **End-to-End Testing**: Complete user workflows
- **Cross-Browser Support**: Chrome, Firefox, Safari, Edge
- **Mobile Testing**: Responsive design validation
- **Screenshot Capture**: Visual regression testing

**Commands**:
```bash
php artisan dusk              # Run all browser tests
php artisan dusk --group=auth # Run specific test group
php artisan dusk:install      # Install/update Chrome driver
```

### Accessibility Testing

**Tools**: axe-core, Lighthouse, Playwright

- **WCAG 2.2 Level AA**: Automated compliance checking
- **Color Contrast**: 4.5:1 ratio validation
- **Keyboard Navigation**: Tab order and focus management
- **Screen Reader**: ARIA labels and semantic HTML
- **Performance**: Core Web Vitals monitoring

**Configuration**: 
- `tests/Browser/AccessibilityTest.js` - Automated a11y tests
- `lighthouse.config.js` - Performance and accessibility audits

**Commands**:
```bash
npm run test:accessibility  # Run Lighthouse audit
npx playwright test         # Run Playwright a11y tests
```

## 📁 Project Structure

```
resources/
├── css/
│   ├── app.css           # Main stylesheet
│   ├── variables.scss    # MOTAC design tokens
│   └── components/       # Component-specific styles
├── js/
│   ├── app.js           # Main JavaScript entry
│   ├── components/      # Reusable JS components
│   └── alpine/          # Alpine.js components
└── views/
    ├── components/      # Blade components
    ├── layouts/         # Layout templates
    └── livewire/        # Livewire components

tests/
├── Browser/             # Laravel Dusk tests
│   ├── AccessibilityTest.js
│   └── ...
└── Feature/             # PHPUnit feature tests

public/
└── build/               # Compiled assets (auto-generated)
```

## 🔧 Development Workflow

### Daily Development

1. **Start Development Environment**:
   ```bash
   composer run dev  # Starts server, queue, logs, and Vite
   ```

2. **Code Quality Checks**:
   ```bash
   npm run quality      # Run linting and formatting
   composer run quality:check  # Check all code quality
   ```

3. **Testing**:
   ```bash
   php artisan test     # Run PHPUnit tests
   php artisan dusk     # Run browser tests
   ```

### Pre-Commit Checklist

- [ ] Code passes all linting rules (`npm run lint`)
- [ ] Code is properly formatted (`npm run format`)
- [ ] PHP code follows Laravel Pint standards
- [ ] All tests pass (`php artisan test`)
- [ ] Accessibility tests pass
- [ ] Assets build successfully (`npm run build`)

### Performance Monitoring

- **Lighthouse Scores**: Accessibility ≥90%, Performance ≥80%
- **Core Web Vitals**: LCP <2.5s, FID <100ms, CLS <0.1
- **Bundle Size**: Monitor asset sizes and optimize
- **Load Times**: Page load <2 seconds requirement

## 🎨 MOTAC Design System

### Colors

```css
/* Primary Colors */
.text-motac-blue    { color: #003366; }
.bg-motac-yellow    { background-color: #FFD700; }
.text-motac-surface { color: #F7F7F7; }

/* Status Colors */
.text-motac-success { color: #27AE60; }
.text-motac-warning { color: #F1C40F; }
.text-motac-error   { color: #E74C3C; }
```

### Typography

```css
/* Font Families */
.font-sans    { font-family: 'Inter', sans-serif; }

/* Font Sizes */
.text-xs      { font-size: 0.75rem; }   /* 12px */
.text-sm      { font-size: 0.875rem; }  /* 14px */
.text-base    { font-size: 1rem; }      /* 16px */
.text-lg      { font-size: 1.125rem; }  /* 18px */
```

### Spacing

```css
/* Consistent spacing scale */
.p-xs    { padding: 0.25rem; }  /* 4px */
.p-sm    { padding: 0.5rem; }   /* 8px */
.p-md    { padding: 1rem; }     /* 16px */
.p-lg    { padding: 1.5rem; }   /* 24px */
```

## 🔍 Troubleshooting

### Common Issues

**Vite not hot reloading**:
```bash
# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

**Dusk tests failing**:
```bash
# Update Chrome driver
php artisan dusk:install
# Check if server is running
php artisan serve
```

**ESLint errors**:
```bash
# Auto-fix common issues
npm run lint:js --fix
```

**Build failures**:
```bash
# Clear all caches
npm run build --clean
php artisan optimize:clear
```

### Performance Issues

- Check bundle analyzer for large dependencies
- Optimize images and assets
- Enable browser caching
- Use CDN for static assets

## 📚 Additional Resources

- [Laravel Vite Documentation](https://laravel.com/docs/vite)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Laravel Dusk Documentation](https://laravel.com/docs/dusk)
- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [Lighthouse Documentation](https://developers.google.com/web/tools/lighthouse)

## 🤝 Contributing

1. Follow the established code style and conventions
2. Run all quality checks before committing
3. Ensure accessibility compliance
4. Add tests for new functionality
5. Update documentation as needed

---

**Note**: This development environment is specifically configured for the ICTServe system requirements and MOTAC design standards. Modifications should maintain compliance with WCAG 2.2 Level AA and performance requirements.
