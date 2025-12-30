---
inclusion:
  always: true
  fileMatchPattern:
    - 'pint.json'
    - '**/*.php'
  applyWhen:
    - Code formatting and style enforcement
    - PSR-12 compliance
    - Pre-commit code quality checks
---

# Laravel Pint Code Formatting Guidelines

Laravel Pint is an opinionated PHP code style fixer built on PHP-CS-Fixer. Ensures PSR-12 compliance.

**Version**: 1.26.0

## MANDATORY Usage

**CRITICAL**: Run Pint before EVERY commit:

```bash
vendor/bin/pint
```

On Windows, if `vendor/bin/pint` opens in another app:

```bash
php vendor/bin/pint
```

## Common Commands

```bash
vendor/bin/pint                 # Fix all files
vendor/bin/pint --test          # Test without fixing
vendor/bin/pint --dirty         # Fix only changed files
vendor/bin/pint -v              # Verbose output
vendor/bin/pint app/Models      # Fix specific directory
```

## ICTServe Configuration

Located in `pint.json`:

```json
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {"syntax": "short"},
        "binary_operator_spaces": {"default": "single_space"},
        "method_chaining_indentation": true,
        "ordered_imports": {"sort_algorithm": "alpha"},
        "single_quote": true
    },
    "exclude": ["vendor", "node_modules", "storage", "bootstrap/cache"]
}
```

## Quality Gates

Before commit:

1. Run `vendor/bin/pint` (required)
2. Run `vendor/bin/phpstan analyse` (required)
3. Run `php artisan test` (required)

## CI/CD Integration

Pint runs automatically in GitHub Actions to enforce code style.

## Best Practices

1. Run Pint before every commit
2. Use `--dirty` flag for faster checks
3. Never commit code that fails Pint
4. Share `pint.json` configuration with team
5. Integrate with IDE for automatic formatting
