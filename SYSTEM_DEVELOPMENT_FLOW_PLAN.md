# Pelan Terperinci: Rajah Aliran Pembangunan Sistem (System Development Flow) ICTServe v3.6.1

Dokumen ini menerangkan perancangan terperinci untuk menghasilkan **Rajah Aliran Pembangunan Sistem** bagi ICTServe v3.6.1.

## 1) Objektif

1. Menghasilkan rajah aliran pembangunan sistem (SDLC) yang mencerminkan **fasa pembangunan** ICTServe seperti ditakrifkan dalam dokumentasi v3.6.1.
2. Memastikan rajah memaparkan **input → proses → output/deliverable** bagi setiap fasa (contoh: dokumen D02/D03, reka bentuk D04, kod+migrasi, ujian, deployment).
3. Mengintegrasikan **kualiti & pematuhan** yang dinyatakan (WCAG 2.2 AA, PSR-12, Larastan/PHPStan, PHPUnit, Playwright, Axe-core) sebagai “quality gates” dalam aliran pembangunan.
4. Menyediakan pecahan khusus untuk **Fasa Pembangunan AI (13 fasa)** yang dirujuk oleh pelan pembangunan (D18 cross-reference) sebagai sub-aliran.

## 2) Skop Rajah

### 2.1 Skop Termasuk
- Aliran pembangunan aras tinggi berasaskan fasa yang dinyatakan:
  - **Inisiasi & Keperluan**
  - **Rekabentuk Sistem**
  - **Pembangunan (Implementation)**
  - **Ujian (Testing)**
  - **Deployment & Maintenance**
- Milestone utama (jadual fasa) termasuk:
  - Setup development (Docker environment, CI/CD pipeline)
  - Pembangunan core & modul
  - Real-time (Reverb)
  - Performance & API (Pulse/Sanctum/SSO opsyen)
  - UI/UX + Accessibility
  - UAT
  - Dokumentasi
  - Deployment
  - Maintenance
- Sub-aliran AI: Fasa 1–13 (Asas → Cloud Hybrid Bedrock)
- Peranan utama (ringkas) yang terlibat mengikut pelan:
  - Project Owner, Project Manager, System Analyst, Lead Developer, Frontend, Backend, QA/Test, DevOps, End User

### 2.2 Di Luar Skop
- Butiran prosedur operasi harian (SOP) pentadbiran sistem di production
- Rajah aliran data (DFD) / model data (ERD) (telah ada deliverable berasingan)
- Butiran konfigurasi CI/CD khusus vendor (contoh: GitHub Actions YAML) kecuali dinyatakan secara eksplisit dalam dokumen

## 3) Sumber Rujukan (v3.6.1)

**Sumber utama (source of truth):**
- `_reference/versions/v3.6.1_D01_SYSTEM_DEVELOPMENT_PLAN.md`
  - Seksyen 4: Proses Pembangunan (4.1–4.5)
  - Seksyen 6: Jadual & Milestone
  - Seksyen 6.1: Fasa Pembangunan AI (13 fasa)

**Sumber sokongan (untuk istilah/artefak):**
- `_reference/versions/v3.6.1_D00_SYSTEM_OVERVIEW.md` (gambaran sistem & komponen)
- `_reference/versions/v3.6.1_D02_BUSINESS_REQUIREMENTS_SPECIFICATION.md` (keperluan perniagaan)
- `_reference/versions/v3.6.1_D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md` (keperluan fungsional & bukan fungsional)
- `_reference/versions/v3.6.1_D04_SOFTWARE_DESIGN_DOCUMENT.md` (seni bina & reka bentuk)
- `_reference/versions/v3.6.1_D09_DATABASE_DOCUMENTATION.md` (skema DB, audit)
- `_reference/versions/v3.6.1_D10_SOURCE_CODE_DOCUMENTATION.md` (organisasi kod & standard)
- `_reference/versions/v3.6.1_D11_TECHNICAL_DESIGN_DOCUMENTATION.md` (infra/deployment)
- `_reference/versions/v3.6.1_D12_UI_UX_DESIGN_GUIDE.md` (piawaian UI/UX)
- `_reference/versions/v3.6.1_D16_BROADCASTING_SETUP.md` (Reverb/Echo)
- `_reference/versions/v3.6.1_D17_QUEUE_MANAGEMENT_HORIZON.md` (queue/Horizon)
- `_reference/versions/v3.6.1_D18_AI_CHATBOT_OLLAMA_BEDROCK.md` (AI cloud-hybrid)

## 4) Keputusan Reka Bentuk Rajah

### 4.1 Format
- Output disediakan dalam fail `SYSTEM_DEVELOPMENT_FLOW_DIAGRAMS.md`.
- Setiap rajah menggunakan `mermaid`:
  - `flowchart TD` untuk aliran utama
  - `flowchart LR` (opsyen) untuk pipeline ringkas

### 4.2 Set Rajah Yang Akan Dihasilkan
1. **SDF-ICT-0 (Overview SDLC)**: Fasa 4.1–4.5 sebagai aliran utama.
2. **SDF-ICT-1 (Milestone & Deliverable Flow)**: Jadual fasa (Seksyen 6) sebagai siri “phase gates”.
3. **SDF-ICT-AI (AI Development Flow)**: Fasa AI 1–13 (Seksyen 6.1).
4. **SDF-ICT-QA (Quality Gates)**: Ujian + static analysis + accessibility gate (dirujuk dalam D01 Seksyen 4.4 & 5).

### 4.3 Prinsip Ketekalan
- Nama fasa dan deliverable dikekalkan seperti dalam D01.
- Elak mendakwa butiran yang tidak dinyatakan (contoh vendor CI). Jika perlu, gunakan label generik seperti “CI/CD pipeline”.
- Gunakan Bahasa Melayu untuk label, selari dengan polisi dokumentasi v3.6.0+.

## 5) Inventori Artefak (Input/Output) Mengikut Fasa

### 5.1 Inisiasi & Keperluan
- Input: temubual, dokumen borang aduan/pinjaman
- Output: Dokumen keperluan (D02, D03)

### 5.2 Rekabentuk Sistem
- Input: D02/D03
- Output: D04, wireframe, reka bentuk DB/ERD (ruj. D09)

### 5.3 Pembangunan (Implementation)
- Input: D04
- Output: kod aplikasi (Laravel), migrations, Livewire/Volt, Filament resources, Reverb/Echo, audit trail, monitoring, API (Sanctum), opsyen SSO, queue/Horizon, AI services

### 5.4 Ujian (Testing)
- Input: kod + spesifikasi
- Output: laporan ujian (Unit/Feature/E2E/Accessibility), UAT sign-off

### 5.5 Deployment & Maintenance
- Input: build artefak + konfigurasi
- Output: sistem deployed + monitoring + operasi penyelenggaraan (cache/optimize/backup)

## 6) Langkah Kerja Terperinci (Extensive Steps)

1. **Ekstrak fasa utama** daripada D01 Seksyen 4.1–4.5.
2. **Ekstrak milestone** daripada D01 Seksyen 6 (jadual fasa dan deliverable).
3. **Ekstrak quality gates** daripada D01 Seksyen 4.4 & 5 (Pint, Larastan, PHPUnit, Playwright, Axe-core, UAT).
4. **Ekstrak sub-aliran AI** daripada D01 Seksyen 6.1 (13 fasa) dan pastikan terhubung ke aliran utama.
5. **Takrifkan peranan** (ringkas) yang memulakan/menyemak artefak (D01 Seksyen 3).
6. **Rangka rajah SDF-ICT-0**: aliran SDLC dan deliverable utama.
7. **Rangka rajah SDF-ICT-1**: milestone pipeline (setup → build → test → docs → deploy).
8. **Rangka rajah SDF-ICT-AI**: 13 fasa AI (grouping 1–4 infra/jobs, 5–8 API/admin/UI, 9–13 email/perf/test/deploy/hybrid).
9. **Rangka rajah SDF-ICT-QA**: paparkan gates (static analysis → unit/feature → e2e → accessibility → UAT) sebagai “kelulusan sebelum deploy”.
10. **Semak konsistensi label**: ejaan, istilah, dan tiada aksara khas yang memecahkan Mermaid.
11. **Semak kebolehbacaan**: node tidak terlalu padat; gunakan subgraph.

## 7) Kriteria Penerimaan (Checklist)

- [ ] Rajah memaparkan fasa 4.1–4.5 (D01) secara jelas.
- [ ] Setiap fasa ada input/output/deliverable minimum.
- [ ] Jadual milestone (Seksyen 6) diwakili sebagai aliran fasa.
- [ ] Sub-aliran AI (Seksyen 6.1) dimasukkan.
- [ ] Ujian & quality gates dimasukkan (Seksyen 4.4 & 5).
- [ ] Semua rajah berjaya dirender sebagai Mermaid (tiada syntax error).

---

Fail output rajah: `SYSTEM_DEVELOPMENT_FLOW_DIAGRAMS.md`
