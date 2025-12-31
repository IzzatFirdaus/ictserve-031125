# Gmail API Setup Guide for ICTServe

## Current Status ✅

The Gmail API integration is **fully implemented** and ready for use with OAuth2 authentication.

## Quick Start

1. **Configure Google Cloud Console** (see [Setup Guide](gmail-integration/setup.md))
2. **Add test users** to OAuth consent screen
3. **Run authorization**: `php artisan gmail:authorize`
4. **Test email sending**: `php artisan gmail:test your@email.com`

## Documentation

- 📖 [Complete Setup Guide](gmail-integration/setup.md)
- 🔐 [OAuth Configuration](gmail-integration/oauth-setup.md)
- 🔧 [Commands Reference](gmail-integration/commands.md)
- 🚨 [Troubleshooting Guide](gmail-integration/troubleshooting.md)

## Key Information

### Current Configuration

- **Project**: `ictserve`
- **Project Owner**: `exatfrds67@gmail.com`
- **OAuth Client ID**: `249298917110-76jtaj45fc7l4qnq44ut5sdiofpodvsn.apps.googleusercontent.com`
- **Authentication**: OAuth2 (not service account)
- **Status**: Testing mode - requires approved test users

### Important Notes

⚠️ **OAuth App in Testing Mode**: Only users added to "Test users" list can authenticate  
⚠️ **No Admin Access**: Cannot use service account due to lack of Google Workspace Admin access  
✅ **SSL Fixed**: CA certificates configured for Windows/XAMPP  
✅ **Commands Ready**: `gmail:authorize` and `gmail:test` available  

### Next Steps

1. **Add Test Users**: Add `izzatfirdaus@motac.gov.my` to test users in Google Cloud Console
2. **Authorize with Gmail**: Use `exatfrds67@gmail.com` (project owner) for initial testing
3. **Test Email Sending**: Verify functionality with test command
4. **Production Planning**: Consider OAuth app verification for broader access

## Error Resolution

The main authentication error was resolved by switching from service account (requires domain-wide delegation) to OAuth2 flow (user-controlled authorization).

**Previous Error**: `"unauthorized_client"` - Service account needs domain-wide delegation  
**Solution**: OAuth2 authentication with test user approval  

## Files Created/Modified

- `app/Services/GmailService.php` - OAuth2 implementation
- `app/Console/Commands/GmailAuthorize.php` - Authorization command  
- `.env` - Updated configuration
- `docs/gmail-integration/` - Complete documentation
