# D00-D15 Standards Compliance Checker

## Overview

The D00-D15 Standards Compliance Checker is a comprehensive automated tool for auditing frontend components against all ICTServe documentation standards (D00-D15). It provides detailed compliance reports, identifies gaps, and recommends remediation actions.

## Features

### Automated Compliance Checking

- **D03 Software Requirements Specification**: Requirements traceability validation
- **D04 Software Design Document**: Design specification links
- **D09 Database Documentation**: Database interaction documentation
- **D10 Source Code Documentation**: PHPDoc blocks and inline documentation
- **D11 Technical Design Documentation**: Performance and technical standards
- **D12 UI/UX Design Guide**: Accessibility and user experience standards
- **D13 UI/UX Frontend Framework**: Responsive design and component structure
- **D14 UI/UX Style Guide**: Color contrast, typography, and MOTAC branding
- **D15 Language Support**: Bilingual support (Bahasa Melayu/English)

### Accessibility Scanning (WCAG 2.2 Level AA)

- Touch target size validation (44×44 pixels minimum)
- Image alt text verification
- Form label association checking
- Keyboard navigation support detection
- ARIA attributes and landmarks validation
- Color contrast ratio checking (4.5:1 minimum)
- Semantic HTML structure analysis

### Documentation Coverage Analysis

- PHPDoc block presence and completeness
- Component metadata header validation
- Usage examples and integration guides
- Requirements traceability links
- Version history and change tracking

### Performance Baseline Measurement

- File size analysis
- Lazy loading implementation checking
- N+1 query detection
- Image optimization verification
- Code splitting recommendations

### Bilingual Support Detection

- Hardcoded text detection
- Translation function usage validation
- Language switcher presence checking
- Locale-specific content verification

## Usage

### Command Line Interface

#### Check All Components

```bash
php artisan compliance:check
```

#### Check Specific Component

```bash
php artisan compliance:check resources/views/components/form/input.blade.php
```

#### Check by Component Type

```bash
# Check only Blade components
php artisan compliance:check --type=blade

# Check only Livewire components
php artisan compliance:check --type=livewire

# Check only email templates
php artisan compliance:check --type=email
```

#### Export Reports

```bash
# Export as JSON
php artisan compliance:check --export=json --output=compliance-report.json

# Export as CSV
php artisan compliance:check --export=csv --output=compliance-report.csv

# Export as HTML
php artisan compliance:check --export=html --output=compliance-report.html
```

### Programmatic Usage

```php
use App\Services\ComplianceAudit\StandardsComplianceChecker;

// Inject the service
public function __construct(
    private StandardsComplianceChecker $complianceChecker
) {}

// Audit a component
$report = $this->complianceChecker->auditComponent(
    'resources/views/components/form/input.blade.php'
);

// Check overall score
$score = $report->getOverallScore(); // 0-100

// Check specific compliance areas
if (!$report->passes('accessibility')) {
    $issues = $report->getAccessibilityIssues();
    // Handle accessibility issues
}

// Get critical issues
$criticalIssues = $report->getCriticalIssues();

// Export report
$reportData = $report->toArray();
```

## Compliance Report Structure

### Report Fields

- **component_path**: Path to the component file
- **component_type**: Type (blade, livewire, volt, email, error, admin)
- **audit_date**: When the audit was performed
- **overall_score**: Overall compliance score (0-100)
- **standards**: List of D00-D15 standards checked
- **checks**: Individual compliance check results
- **critical_issues**: List of critical issues requiring immediate attention

### Check Fields

- **name**: Check identifier
- **passed**: Boolean indicating pass/fail
- **issues**: Array of specific issues found
- **metadata**: Additional check-specific data
- **severity**: Issue severity (critical, high, medium, low)
- **recommendation**: Suggested remediation action

## Compliance Checks

### Accessibility Check

**Severity**: Critical

**Validates**:
- Touch target sizes (44×44 pixels minimum)
- Image alt text attributes
- Form label associations
- Keyboard navigation support
- ARIA attributes and landmarks

**Example Issues**:
- "Interactive elements may not meet 44×44 pixel minimum touch target size"
- "Images missing alt text attributes"
- "Form inputs missing associated labels"

### Semantic HTML Check

**Severity**: Medium

**Validates**:
- HTML5 semantic elements usage (header, nav, main, article, section, aside, footer)
- Proper element nesting
- Avoidance of "div soup"

**Example Issues**:
- "Excessive div usage without semantic HTML5 elements"

### ARIA Labels Check

**Severity**: High

**Validates**:
- ARIA landmark roles
- ARIA labels on interactive elements
- ARIA attributes for form validation

**Example Issues**:
- "Interactive elements missing ARIA labels"
- "Form elements missing ARIA attributes for error handling"

### Responsive Design Check

**Severity**: Medium

**Validates**:
- Responsive Tailwind classes (sm:, md:, lg:, xl:, 2xl:)
- Viewport meta tag in layouts
- Mobile-first design approach

**Example Issues**:
- "No responsive design classes found"
- "Layout missing viewport meta tag"

### Color Contrast Check

**Severity**: High

**Validates**:
- Text color contrast ratios (4.5:1 minimum)
- Background color combinations
- Accessible color palette usage

**Example Issues**:
- "Yellow text may not meet 4.5:1 contrast ratio"
- "Light gray text may not meet 4.5:1 contrast ratio on white"

### Typography Check

**Severity**: Low

**Validates**:
- Heading hierarchy (h1, h2, h3, etc.)
- Line height for readability (1.5 minimum)
- Font size accessibility

**Example Issues**:
- "Heading hierarchy skipped (h3 without h2)"
- "Line height may be too tight for readability"

### Branding Check

**Severity**: Medium

**Validates**:
- MOTAC brand color usage
- Logo presence in headers/navigation
- Consistent visual identity

**Example Issues**:
- "Header/navigation missing MOTAC logo or branding"

### Bilingual Support Check

**Severity**: High

**Validates**:
- Translation function usage (__(), @lang(), trans())
- Hardcoded text detection
- Language switcher presence

**Example Issues**:
- "Hardcoded text found - should use translation functions"
- "Layout missing language switcher component"

### Documentation Check

**Severity**: Medium

**Validates**:
- PHPDoc blocks
- Blade component @props definitions
- Component documentation comments

**Example Issues**:
- "Missing PHPDoc documentation blocks"
- "Blade component missing @props definition or documentation comments"

### Metadata Check

**Severity**: Medium

**Validates**:
- Component metadata headers
- Required tags: @component, @description, @author, @updated, @trace

**Example Issues**:
- "Missing metadata: Component Name (@component)"
- "Missing metadata: Description (@description)"

### Requirements Traceability Check

**Severity**: Low

**Validates**:
- @trace tags linking to D03 requirements
- @requirement tags linking to D04 design specs

**Example Issues**:
- "Missing requirements traceability links (@trace or @requirement tags)"

### Performance Check

**Severity**: Medium

**Validates**:
- File size (50KB threshold)
- Lazy loading implementation
- Eager loading for database queries
- Image lazy loading

**Example Issues**:
- "Component file size exceeds 50KB - consider code splitting"
- "Livewire component missing lazy loading attribute"
- "Potential N+1 query issue - missing eager loading"

## Scoring System

### Overall Score Calculation

The overall compliance score is calculated as:

```
Overall Score = (Passed Checks / Total Checks) × 100
```

### Score Interpretation

- **90-100%**: Excellent compliance
- **80-89%**: Good compliance (passing)
- **60-79%**: Fair compliance (needs improvement)
- **0-59%**: Poor compliance (failing)

### Severity Levels

- **Critical**: Must be fixed immediately (accessibility, security)
- **High**: Should be fixed soon (usability, major standards violations)
- **Medium**: Should be addressed (documentation, minor standards violations)
- **Low**: Nice to have (optimization, best practices)

## Integration with CI/CD

### GitHub Actions Example

```yaml
name: Compliance Check

on: [push, pull_request]

jobs:
  compliance:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Compliance Check
        run: php artisan compliance:check --export=json --output=compliance-report.json
      - name: Upload Report
        uses: actions/upload-artifact@v2
        with:
          name: compliance-report
          path: compliance-report.json
```

### Pre-commit Hook Example

```bash
#!/bin/bash
# .git/hooks/pre-commit

# Get list of staged PHP and Blade files
FILES=$(git diff --cached --name-only --diff-filter=ACM | grep -E '\.(php|blade\.php)$')

if [ -n "$FILES" ]; then
    echo "Checking compliance for modified components..."
    
    for FILE in $FILES; do
        php artisan compliance:check "$FILE"
        
        if [ $? -ne 0 ]; then
            echo "Compliance check failed for $FILE"
            exit 1
        fi
    done
fi

exit 0
```

## Best Practices

### Component Development

1. **Start with Compliant Templates**: Use existing compliant components as templates
2. **Check Early and Often**: Run compliance checks during development
3. **Fix Critical Issues First**: Prioritize accessibility and security issues
4. **Document as You Code**: Add metadata headers and PHPDoc blocks immediately
5. **Test Accessibility**: Use screen readers and keyboard navigation

### Remediation Workflow

1. **Run Full Audit**: `php artisan compliance:check`
2. **Review Critical Issues**: Focus on critical and high severity issues
3. **Fix Component by Component**: Address one component at a time
4. **Verify Fixes**: Re-run compliance check after fixes
5. **Document Changes**: Update component metadata and version history

### Maintenance

1. **Schedule Regular Audits**: Run weekly or monthly compliance checks
2. **Monitor Trends**: Track compliance scores over time
3. **Update Standards**: Keep checker rules aligned with latest D00-D15 standards
4. **Train Team**: Ensure all developers understand compliance requirements
5. **Automate**: Integrate compliance checks into CI/CD pipeline

## Troubleshooting

### Common Issues

**Issue**: "Component file not found"
**Solution**: Verify the file path is correct and relative to project root

**Issue**: "False positive for hardcoded text"
**Solution**: Ensure translation functions are properly formatted: `{{ __('key') }}`

**Issue**: "Performance check fails for large components"
**Solution**: Consider code splitting or lazy loading for components >50KB

**Issue**: "Accessibility check fails but component is accessible"
**Solution**: Ensure proper ARIA attributes and semantic HTML are used

### Getting Help

- Review D00-D15 documentation in `docs/` directory
- Check existing compliant components for examples
- Consult with accessibility team for WCAG guidance
- Review Laravel and Livewire documentation for best practices

## Related Documentation

- [D10 Source Code Documentation](../D10_SOURCE_CODE_DOCUMENTATION.md)
- [D12 UI/UX Design Guide](../D12_UI_UX_DESIGN_GUIDE.md)
- [D14 UI/UX Style Guide](../D14_UI_UX_STYLE_GUIDE.md)
- [D15 Language Support](../D15_LANGUAGE_MS_EN.md)
- [Accessibility Guidelines](./accessibility-guidelines.md)
- [Component Development Guide](./component-development-guide.md)

