# Gmail API Integration for ICTServe

This directory contains documentation for setting up and using Gmail API integration in ICTServe.

## Overview

ICTServe uses Gmail API to send emails through Google's infrastructure instead of traditional SMTP. This provides better deliverability and integration with Google Workspace.

## Documentation Files

- [Setup Guide](setup.md) - Complete setup instructions
- [OAuth Configuration](oauth-setup.md) - OAuth2 authentication setup
- [Troubleshooting](troubleshooting.md) - Common issues and solutions
- [Commands Reference](commands.md) - Available Artisan commands

## Quick Start

1. Follow the [Setup Guide](setup.md) to configure Google Cloud Console
2. Run `php artisan gmail:authorize` to authenticate
3. Test with `php artisan gmail:test your@email.com`

## Current Status

✅ Gmail API integration code is complete  
✅ OAuth2 authentication implemented  
⚠️ Requires Google Cloud Console configuration  
⚠️ OAuth app in testing mode - requires test user approval  

## Support

For issues or questions, refer to the [Troubleshooting Guide](troubleshooting.md).
