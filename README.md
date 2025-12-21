# Dokumentasi ICTServe

Dokumentasi lengkap untuk sistem ICTServe - Platform Pengurusan ICT BPM MOTAC.

**Last Updated**: 2025-12-17  
**Version**: 3.6.1  
**Bahasa**: Bahasa Melayu (utama), istilah teknikal English bila perlu

## Navigasi Pantas

### Penempatan Docker

- **[Docker Documentation](docker/README.md)** - Ringkasan dan mula pantas
  - [Setup Guide](docker/SETUP.md) - Pemasangan lengkap
  - **[Composer Issues Fixed](docker/COMPOSER_ISSUES_FIXED.md)** - ✅ Isu composer diselesaikan
  - [Quick Reference](docker/QUICK_REFERENCE.md) - Rujukan pantas
  - [Architecture](docker/ARCHITECTURE.md) - Reka bentuk kontena
  - [Troubleshooting](docker/TROUBLESHOOTING.md) - Isu lazim
  - [Windows Guide](docker/WINDOWS.md) - Arahan khusus Windows
  - [Container Specs](docker/container-specs.md) - Spesifikasi kontena

### Dokumentasi Sistem (D00–D18)

- [D00 - System Overview](D00_SYSTEM_OVERVIEW.md)
- [D01 - Development Plan](D01_SYSTEM_DEVELOPMENT_PLAN.md)
- [D02 - Business Requirements](D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
- [D03 - Software Requirements](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 - Software Design](D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D05 - Data Migration Plan](D05_DATA_MIGRATION_PLAN.md)
- [D06 - Data Migration Spec](D06_DATA_MIGRATION_SPECIFICATION.md)
- [D07 - Integration Plan](D07_SYSTEM_INTEGRATION_PLAN.md)
- [D08 - Integration Spec](D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D09 - Database Documentation](D09_DATABASE_DOCUMENTATION.md)
- [D10 - Source Code Documentation](D10_SOURCE_CODE_DOCUMENTATION.md)
- [D11 - Technical Design](D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [D12 - UI/UX Design Guide](D12_UI_UX_DESIGN_GUIDE.md)
- [D13 - Frontend Framework](D13_UI_UX_FRONTEND_FRAMEWORK.md)
- [D14 - Style Guide](D14_UI_UX_STYLE_GUIDE.md)
- [D15 - Piawaian Bahasa](D15_LANGUAGE_MS_EN.md)
- [D16 - Broadcasting Setup](D16_BROADCASTING_SETUP.md)
- [D17 - Queue Management (Redis)](D17_QUEUE_MANAGEMENT_HORIZON.md)
- [D18 - AI Chatbot Ollama-Bedrock](D18_AI_CHATBOT_OLLAMA_BEDROCK.md)

### Rujukan Teknikal

- **[Reference Documentation](reference/README.md)** - Panduan setup dan prosedur operasi
  - [Laragon Setup](reference/laragon-setup.md)
  - [Laravel Boost Setup](reference/laravel-boost-setup.md)
  - [Virtual Host Setup](reference/vhost-setup-guide.md)
  - [Deployment Checklist](reference/deployment-checklist.md)
  - [Performance Guide](reference/performance-optimization-guide.md)
  - [Production Troubleshooting](reference/troubleshooting-production.md)

### Sumber Tambahan

- [Glosari](GLOSSARY.md)
- [Indeks Lengkap](INDEX.md)
- [Dokumentasi Induk](ICTServe_System_Documentation.md)

## Struktur Dokumentasi

```text
docs/
├── docker/              # Panduan penempatan Docker
├── reference/           # Rujukan teknikal (ops & dev)
│   ├── rtm/             # Requirements traceability
│   └── legacy/          # Dokumen arkib
├── mcp/                 # Dokumentasi MCP server
├── frontend/            # Panduan frontend
├── security/            # Dokumentasi keselamatan
├── api/                 # Dokumentasi API
└── D00-D18*.md          # Dokumen reka bentuk sistem
```

## Standard Dokumentasi

- SemVer untuk versi dokumen
- Markdown untuk kebolehbacaan
- Bahasa Melayu (UI) dan istilah teknikal English bila perlu
- Traceability merentas D00–D18

---

**Diselenggara Oleh**: Pasukan ICT BPM MOTAC
