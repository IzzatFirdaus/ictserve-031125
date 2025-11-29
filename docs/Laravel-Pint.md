# Laravel Pint — Code Style Fixer

## Overview

Laravel Pint is an opinionated PHP code style fixer built on top of PHP-CS-Fixer. It ensures your code follows consistent styling conventions with zero configuration required.

**Version**: Laravel 12.x compatible  
**Purpose**: Automatic code formatting (PSR-12 compliant)

## Installation

Pint is included with Laravel by default. For standalone projects:

```bash
composer require laravel/pint --dev
```

## Basic Usage

### Fix All Files

```bash
vendor/bin/pint
```

### Test Without Fixing

```bash
vendor/bin/pint --test
```

### Fix Specific Files/Directories

```bash
vendor/bin/pint app/Models
vendor/bin/pint app/Models/User.php
```

### Show Changes (Verbose)

```bash
vendor/bin/pint -v
```

### Very Verbose Output

```bash
vendor/bin/pint -vv
```

## Configuration

Create `pint.json` in project root:

```json
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": false,
        "new_with_braces": {
            "anonymous_class": false,
            "named_class": false
        }
    }
}
```

### Available Presets

- `laravel` (default)
- `psr12`
- `symfony`
- `per`

### Using Different Preset

```json
{
    "preset": "psr12"
}
```

## Rules Configuration

### Enable/Disable Rules

```json
{
    "preset": "laravel",
    "rules": {
        "concat_space": {
            "spacing": "one"
        },
        "method_chaining_indentation": true,
        "not_operator_with_successor_space": true
    }
}
```

### Exclude Rules

```json
{
    "preset": "laravel",
    "rules": {
        "braces": false,
        "single_line_after_imports": false
    }
}
```

## Excluding Files

### Exclude Paths

```json
{
    "exclude": [
        "vendor",
        "node_modules",
        "storage",
        "bootstrap/cache"
    ]
}
```

### Exclude Specific Files

```json
{
    "exclude": [
        "app/Helpers/legacy.php",
        "database/migrations/*"
    ]
}
```

## Common Rules

### Array Syntax

```json
{
    "rules": {
        "array_syntax": {
            "syntax": "short"
        }
    }
}
```

### Binary Operator Spaces

```json
{
    "rules": {
        "binary_operator_spaces": {
            "default": "single_space"
        }
    }
}
```

### Blank Line After Opening Tag

```json
{
    "rules": {
        "blank_line_after_opening_tag": true
    }
}
```

### Method Chaining

```json
{
    "rules": {
        "method_chaining_indentation": true
    }
}
```

### Ordered Imports

```json
{
    "rules": {
        "ordered_imports": {
            "sort_algorithm": "alpha"
        }
    }
}
```

## Integration with Composer

Add to `composer.json`:

```json
{
    "scripts": {
        "pint": "pint",
        "pint:test": "pint --test",
        "pint:dirty": "pint --dirty"
    }
}
```

Usage:

```bash
composer pint
composer pint:test
```

## Git Integration

### Pre-commit Hook

Create `.git/hooks/pre-commit`:

```bash
#!/bin/sh

# Run Pint on staged files
FILES=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')

if [ -n "$FILES" ]; then
    vendor/bin/pint $FILES
    git add $FILES
fi
```

Make executable:

```bash
chmod +x .git/hooks/pre-commit
```

### Fix Only Changed Files

```bash
vendor/bin/pint --dirty
```

## CI/CD Integration

### GitHub Actions

```yaml
name: Code Style

on: [push, pull_request]

jobs:
  pint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      
      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist
      
      - name: Run Pint
        run: vendor/bin/pint --test
```

### GitLab CI

```yaml
pint:
  stage: test
  script:
    - composer install --no-interaction --prefer-dist
    - vendor/bin/pint --test
```

## ICTServe Configuration

Recommended `pint.json` for ICTServe:

```json
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {
            "syntax": "short"
        },
        "binary_operator_spaces": {
            "default": "single_space"
        },
        "blank_line_after_opening_tag": true,
        "concat_space": {
            "spacing": "one"
        },
        "method_chaining_indentation": true,
        "no_unused_imports": true,
        "ordered_imports": {
            "sort_algorithm": "alpha"
        },
        "single_quote": true,
        "trailing_comma_in_multiline": true
    },
    "exclude": [
        "vendor",
        "node_modules",
        "storage",
        "bootstrap/cache"
    ]
}
```

## Common Patterns

### Fix Before Commit

```bash
# Fix all files
vendor/bin/pint

# Stage changes
git add .

# Commit
git commit -m "Apply code style fixes"
```

### Fix Specific Module

```bash
vendor/bin/pint app/Filament
vendor/bin/pint app/Livewire
vendor/bin/pint app/Models
```

### Check Style in CI

```bash
vendor/bin/pint --test || exit 1
```

## Best Practices

1. **Run Before Commit**: Always run Pint before committing code
2. **Use Pre-commit Hooks**: Automate style fixing with Git hooks
3. **CI Integration**: Enforce style checks in CI/CD pipeline
4. **Team Consistency**: Share `pint.json` configuration with team
5. **Exclude Generated Files**: Don't format auto-generated code

## Troubleshooting

### Pint Not Found

```bash
composer require laravel/pint --dev
```

### Permission Denied

```bash
chmod +x vendor/bin/pint
```

### Memory Limit

```bash
php -d memory_limit=512M vendor/bin/pint
```

## VS Code Integration

Install PHP CS Fixer extension and configure:

```json
{
    "php-cs-fixer.executablePath": "${workspaceFolder}/vendor/bin/pint",
    "php-cs-fixer.onsave": true
}
```

## PhpStorm Integration

1. Go to Settings → Tools → External Tools
2. Add new tool:
   - Name: Laravel Pint
   - Program: `$ProjectFileDir$/vendor/bin/pint`
   - Arguments: `$FilePath$`
   - Working directory: `$ProjectFileDir$`

## Testing

Verify Pint configuration:

```bash
# Test without making changes
vendor/bin/pint --test -v

# Show what would be fixed
vendor/bin/pint --test -vv
```

## Performance

### Parallel Processing

Pint automatically uses multiple CPU cores for faster processing.

### Cache

Pint caches results for faster subsequent runs.

Clear cache:

```bash
rm -rf .php-cs-fixer.cache
```

## References

- Official Documentation: https://laravel.com/docs/12.x/pint
- GitHub Repository: https://github.com/laravel/pint
- PHP-CS-Fixer: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer
