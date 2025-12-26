# Percy Visual Testing Setup for ICTServe v3.6.1

This document provides setup instructions for Percy visual testing integration with the ICTServe v3.6.1 Laravel application.

## Prerequisites

- Node.js 18+ (already configured in the project)
- Playwright 1.56.1 (already installed)
- Laravel 12.43.1 (current project version)
- Percy account (BrowserStack Percy)

## 1. Percy Account Setup

### Create Percy Project

1. Visit [Percy.io](https://percy.io/) and sign up or log in
2. Create a new project named `ictserve`
3. Copy your Percy token from the project settings

### Alternative: BrowserStack Percy

1. Visit [BrowserStack Percy](https://percy.browserstack.com/)
2. Sign up or log in with your BrowserStack account
3. Create a new project for ICTServe v3.6.1
4. Copy your Percy token

## 2. Environment Configuration

### Update .env File

Add the following Percy configuration to your `.env` file:

```bash
# Percy Visual Testing Configuration
PERCY_TOKEN=your_percy_token_here
PERCY_PROJECT=ictserve
PERCY_ENABLED=true
PERCY_BRANCH=main
PERCY_TARGET_BRANCH=main
```

### Validate Configuration

Run the Percy configuration validation command:

```bash
php artisan percy:validate-config --show-config
```

This command will:

- ✅ Validate your Percy token and project settings
- 📋 Show current configuration
- 🛠️ Display technology stack information
- 💡 Provide setup guidance if configuration is invalid

## 3. Dependencies Installation

The required dependencies are already installed:

- `@percy/cli` - Percy command-line interface
- `@percy/playwright` - Percy integration for Playwright

If you need to reinstall:

```bash
npm install --save-dev @percy/cli @percy/playwright
```

## 4. Configuration Files

### percy.config.js

The Percy configuration file includes:

- Responsive breakpoints (375, 768, 1024, 1280, 1920px)
- CSS to hide dynamic content
- ICTServe v3.6.1 specific settings
- Bahasa Melayu interface support
- True Hybrid Architecture compatibility

### config/percy.php

Laravel configuration file with:

- Environment-specific settings
- ICTServe technology stack configuration
- WCAG 2.2 AA compliance settings
- Error handling and graceful degradation
- Bahasa Melayu error messages

## 5. Running Percy Tests

### Basic Test Execution

```bash
# Run all Playwright tests with Percy
npm run test:e2e:percy

# Run specific test file with Percy
npm run test:e2e:percy:helpdesk

# Run accessibility tests with Percy
npm run test:accessibility:percy
```

### Setup Validation Test

Run the Percy setup validation test:

```bash
# With Percy enabled
npm run test:e2e:percy -- percy-setup-validation.spec.ts

# Without Percy (fallback test)
npm run test:e2e -- percy-setup-validation.spec.ts
```

### Manual Percy Commands

```bash
# Start Percy build manually
npm run percy:build

# Execute tests with Percy
npm run percy:exec -- playwright test

# Finalize Percy build
npm run percy:finalize
```

## 6. ICTServe v3.6.1 Specific Features

### True Hybrid Architecture Support

Percy is configured to handle:

- **Guest user workflows** - Forms and status pages without authentication
- **Authenticated user workflows** - Dashboard and profile pages
- **Admin workflows** - Filament admin panel components

### Bahasa Melayu Interface

- Language switcher is hidden in snapshots (v3.6.0+ exclusive Bahasa Melayu)
- Error messages are provided in Bahasa Melayu
- Interface validation ensures Bahasa Melayu consistency

### Technology Stack Integration

Percy configuration includes metadata for:

- Laravel 12.43.1
- Livewire 3.7.3 (dynamic content handling)
- Filament 4.3.1 (admin panel components)
- Playwright 1.56.1
- Tailwind CSS 4.1.18

## 7. Troubleshooting

### Common Issues

1. **Percy token not found**

   ```bash
   php artisan percy:validate-config
   ```

   Add `PERCY_TOKEN` to your `.env` file.

2. **Tests run without Percy snapshots**
   - Check `PERCY_ENABLED=true` in `.env`
   - Verify Percy token is valid
   - Run validation command

3. **Snapshots show dynamic content**
   - Review `percy.config.js` CSS rules
   - Add selectors to hide dynamic elements
   - Check Livewire loading states

### Debug Mode

Enable Percy debug mode in your `.env`:

```bash
PERCY_ENABLED=true
APP_DEBUG=true
```

Run tests with debug output:

```bash
DEBUG=percy* npm run test:e2e:percy
```

### Graceful Degradation

If Percy services are unavailable, tests will continue without visual captures. Check logs:

```bash
tail -f storage/logs/laravel.log | grep percy
```

## 8. CI/CD Integration

### GitHub Actions

Add Percy token to GitHub repository secrets:

- Secret name: `PERCY_TOKEN`
- Value: Your Percy token

### Environment Variables for CI

```yaml
env:
  PERCY_TOKEN: ${{ secrets.PERCY_TOKEN }}
  PERCY_BRANCH: ${{ github.head_ref }}
  PERCY_TARGET_BRANCH: main
  PERCY_PARALLEL_NONCE: ${{ github.run_id }}
  PERCY_PARALLEL_TOTAL: 4
```

## 9. Next Steps

After completing the setup:

1. ✅ Validate configuration with `php artisan percy:validate-config`
2. 🧪 Run setup validation test
3. 📸 Integrate Percy with existing Playwright tests
4. 🔄 Set up CI/CD pipeline integration
5. 📊 Review Percy dashboard for visual comparisons

## Support

For issues specific to ICTServe v3.6.1 Percy integration:

- Check Laravel logs: `storage/logs/laravel.log`
- Run configuration validation: `php artisan percy:validate-config`
- Review Percy dashboard: [percy.io](https://percy.io/)

For Percy platform support:

- [Percy Documentation](https://docs.percy.io/)
- [BrowserStack Percy Support](https://www.browserstack.com/support)
