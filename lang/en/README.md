# English Translation Files (v3.6.0)

**STATUS:** RETAINED FOR TECHNICAL REFERENCE ONLY

As of v3.6.0, ICTServe uses Bahasa Melayu-only interface.
These English translation files are retained for:

- Technical reference and documentation
- Potential future bilingual support restoration
- Developer understanding of system terminology

**DO NOT DELETE** these files without approval.

## Version History

| Version | Date | Language Support |
|---------|------|------------------|
| v3.5.0 | 2025-11 | Bilingual (Bahasa Melayu + English) |
| v3.6.0 | 2025-12 | Bahasa Melayu only |

## Deprecated Components (v3.6.0)

The following components have been deprecated as part of the Bahasa Melayu-only transition:

- `LanguageSwitcher` Livewire component (deleted)
- `BilingualSupportService` (deprecated, returns 'ms' only)
- `SetLocale` middleware (deprecated, always sets 'ms')
- `users.locale` column (deprecated, always returns 'ms')
- `ictserve_locale` cookie (removed)

## Contact

For questions about language support, contact BPM MOTAC ICT Team.
