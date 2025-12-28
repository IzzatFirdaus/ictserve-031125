# Test Data Directory

This directory contains test data and fixtures used by the automation scripts.

## Directory Structure

```
test-data/
├── users/              # User test data (credentials, profiles)
├── tickets/            # Helpdesk ticket test data
├── assets/             # Asset and loan test data
├── ai-conversations/   # AI conversation test data
└── documents/          # Test documents and files for upload testing
```

## Usage

Test data is automatically loaded by scripts using the `Get-TestDataPath` function from `utilities/common-functions.ps1`.

## Security Note

- Do not commit real credentials to this directory
- Use placeholder data for sensitive fields
- Test credentials should be stored in `config/credentials.json`
