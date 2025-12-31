# OAuth2 Configuration for Gmail API

## Overview

ICTServe uses OAuth2 authentication to access Gmail API on behalf of users. This document explains the OAuth2 flow and configuration.

## OAuth2 Flow

1. **Authorization Request**: Application redirects user to Google's authorization server
2. **User Consent**: User grants permissions to the application
3. **Authorization Code**: Google returns an authorization code
4. **Token Exchange**: Application exchanges code for access/refresh tokens
5. **API Access**: Application uses tokens to make Gmail API calls

## Configuration Details

### OAuth Client Type

**Desktop Application** is used because:

- ICTServe runs as a server application
- Uses "out-of-band" (OOB) redirect URI
- Suitable for applications that can't receive HTTP redirects

### Redirect URI

```
urn:ietf:wg:oauth:2.0:oob
```

This special URI tells Google to display the authorization code directly to the user instead of redirecting to a web endpoint.

### Required Scopes

```
https://www.googleapis.com/auth/gmail.send
```

This scope allows the application to:

- Send emails on behalf of the authenticated user
- Does NOT allow reading emails or other Gmail data

## Test Users vs Production

### Testing Mode (Current)

- OAuth app is in "Testing" status
- Only approved test users can authorize
- No Google verification required
- Suitable for development and internal use

**Test Users Configuration:**

1. Go to Google Cloud Console → OAuth consent screen
2. Add users in "Test users" section
3. Users must be explicitly added to access the app

### Production Mode (Future)

To make the app available to all users:

1. **Verification Process**: Google requires app verification
2. **Privacy Policy**: Must provide privacy policy URL
3. **Terms of Service**: Must provide terms of service URL
4. **Security Review**: Google reviews the application
5. **Publishing**: App becomes available to all Google users

## Current Project Configuration

### Project Details

- **Project ID**: `ictserve`
- **Project Owner**: `exatfrds67@gmail.com`
- **OAuth Client ID**: `249298917110-76jtaj45fc7l4qnq44ut5sdiofpodvsn.apps.googleusercontent.com`

### Service Account (Not Used)

- **Service Account**: `ictserve-gmail-service@ictserve.iam.gserviceaccount.com`
- **Client ID**: `106770287656352172713`
- **Status**: Available but not used (requires domain-wide delegation)

## Security Considerations

### Token Storage

- Access tokens are stored in `storage/app/gmail_token.json`
- Refresh tokens allow automatic token renewal
- Tokens are encrypted at rest by Laravel's storage system

### Token Lifecycle

- **Access Token**: Valid for 1 hour
- **Refresh Token**: Valid until revoked
- **Automatic Refresh**: Handled by Google Client Library

### Access Control

- Only authenticated users can send emails
- Emails are sent from the authenticated user's account
- No impersonation or domain-wide access

## Troubleshooting OAuth Issues

### Error: "access_denied"

- User is not added as a test user
- Add user to test users list in Google Cloud Console

### Error: "redirect_uri_mismatch"

- Redirect URI not configured in OAuth client
- Add `urn:ietf:wg:oauth:2.0:oob` to authorized redirect URIs

### Error: "invalid_client"

- Client ID or Client Secret is incorrect
- Verify credentials in Google Cloud Console

### Error: "unauthorized_client"

- OAuth client not properly configured
- Check OAuth consent screen configuration

## Migration from Service Account

The original implementation used service account with domain-wide delegation:

**Why Changed:**

- Requires Google Workspace Admin access
- User cannot access `admin.google.com`
- Domain-wide delegation setup not possible

**OAuth2 Advantages:**

- No admin access required
- User controls their own authorization
- More secure (no impersonation)
- Easier to set up and maintain

## Future Enhancements

### Multi-User Support

- Store tokens per user
- Allow multiple users to authenticate
- User-specific email sending

### Token Management

- Admin interface for token management
- Token revocation functionality
- Token status monitoring

### Production Deployment

- Complete Google verification process
- Implement proper privacy policy
- Add terms of service
- Security audit and review
