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

- [D00 - System Overview](docs/D00_SYSTEM_OVERVIEW.md)
- [D01 - Development Plan](docs/D01_SYSTEM_DEVELOPMENT_PLAN.md)
- [D02 - Business Requirements](docs/D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md)
- [D03 - Software Requirements](docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md)
- [D04 - Software Design](docs/D04_SOFTWARE_DESIGN_DOCUMENT.md)
- [D05 - Data Migration Plan](docs/D05_DATA_MIGRATION_PLAN.md)
- [D06 - Data Migration Spec](docs/D06_DATA_MIGRATION_SPECIFICATION.md)
- [D07 - Integration Plan](docs/D07_SYSTEM_INTEGRATION_PLAN.md)
- [D08 - Integration Spec](docs/D08_SYSTEM_INTEGRATION_SPECIFICATION.md)
- [D09 - Database Documentation](docs/D09_DATABASE_DOCUMENTATION.md)
- [D10 - Source Code Documentation](docs/D10_SOURCE_CODE_DOCUMENTATION.md)
- [D11 - Technical Design](docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md)
- [D12 - UI/UX Design Guide](docs/D12_UI_UX_DESIGN_GUIDE.md)
- [D13 - Frontend Framework](docs/D13_UI_UX_FRONTEND_FRAMEWORK.md)
- [D14 - Style Guide](docs/D14_UI_UX_STYLE_GUIDE.md)
- [D15 - Piawaian Bahasa](docs/D15_LANGUAGE_MS_EN.md)
- [D16 - Broadcasting Setup](docs/D16_BROADCASTING_SETUP.md)
- [D17 - Queue Management (Redis)](docs/D17_QUEUE_MANAGEMENT_HORIZON.md)
- [D18 - AI Chatbot Ollama-Bedrock](docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md)

### Rujukan Teknikal

- **[Reference Documentation](reference/README.md)** - Panduan setup dan prosedur operasi
  - [Laragon Setup](reference/laragon-setup.md)
  - [Laravel Boost Setup](reference/laravel-boost-setup.md)
  - [Virtual Host Setup](reference/vhost-setup-guide.md)
  - [Deployment Checklist](reference/deployment-checklist.md)
  - [Performance Guide](reference/performance-optimization-guide.md)
  - [Production Troubleshooting](reference/troubleshooting-production.md)

### Sumber Tambahan

- [Glosari](docs/GLOSSARY.md)
- [Indeks Lengkap](docs/INDEX.md)
- [Dokumentasi Induk](docs/ICTServe_System_Documentation.md)

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
