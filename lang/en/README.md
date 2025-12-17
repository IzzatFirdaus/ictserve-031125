# English Translation Files (v3.6.1)

**STATUS:** RETAINED FOR TECHNICAL REFERENCE ONLY

As of v3.6.0, ICTServe uses Bahasa Melayu-only interface.
These English translation files are retained for:

- Technical reference and documentation
- Potential future bilingual support restoration
- Developer understanding of system terminology
- AI Chatbot terminology reference (v3.6.1)

**DO NOT DELETE** these files without approval.

## Version History

| Version | Date | Language Support | Notes |
|---------|------|------------------|-------|
| v3.5.0 | 2025-11 | Bilingual (Bahasa Melayu + English) | Full bilingual support |
| v3.6.0 | 2025-12 | Bahasa Melayu only | Language switcher disabled |
| v3.6.1 | 2025-12 | Bahasa Melayu only | AI Chatbot integration (D18 v1.0.1) |

## Deprecated Components (v3.6.0)

The following components have been deprecated as part of the Bahasa Melayu-only transition:

- `LanguageSwitcher` Livewire component (deleted)
- `BilingualSupportService` (deprecated, returns 'ms' only)
- `SetLocale` middleware (deprecated, always sets 'ms')
- `users.locale` column (deprecated, always returns 'ms')
- `ictserve_locale` cookie (removed)

## File Maintenance (v3.6.1)

All PHP files in this directory include the following header comment:

```php
// RETAINED FOR TECHNICAL REFERENCE ONLY (v3.6.0)
// See lang/en/README.md for details
```

## Related Documentation

- **D15**: `docs/D15_LANGUAGE_MS_EN.md` - Language documentation
- **D18**: `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md` - AI Chatbot integration

## Contact

For questions about language support, contact BPM MOTAC ICT Team.
