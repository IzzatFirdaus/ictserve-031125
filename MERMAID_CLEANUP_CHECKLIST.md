# Senarai Semak Pantas - Pembersihan Rajah Mermaid

**Rujukan**: `MERMAID_DIAGRAM_CLEANUP_INSTRUCTIONS.md` untuk panduan lengkap

---

## ✅ Senarai Fail Perlu Dibersihkan

### Root Directory
- [ ] `AI_CHATBOT_FLOW_DIAGRAMS.md`
- [ ] `DFD_DIAGRAMS.md`
- [ ] `ERD_DIAGRAMS.md`
- [ ] `MANUAL_TO_SYSTEM_FLOW_DIAGRAMS.md`
- [ ] `SYSTEM_DEVELOPMENT_FLOW_DIAGRAMS.md`
- [x] `SWIMLANE_DIAGRAMS.md` ✅ **SELESAI** (Rajah 1, 2, 3)
- [ ] `WORKFLOW_DIAGRAMS.md`

### docs/
- [ ] `docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md`
- [ ] `docs/ICTServe_System_Documentation.md`

### docs/KRISA/
- [ ] `docs/KRISA/D01_KRISA_ICTSERVE_PELAN_PEMBANGUNAN_SISTEM.md`
- [ ] `docs/KRISA/D03_KRISA_ICTSERVE_SPESIFIKASI_KEPERLUAN_SISTEM.md`
- [ ] `docs/KRISA/D04_KRISA_ICTSERVE_SPESIFIKASI_REKABENTUK_SISTEM.md`
- [ ] `docs/KRISA/D05_KRISA_ICTSERVE_PELAN_MIGRASI_DATA.md`
- [ ] `docs/KRISA/D06_KRISA_ICTSERVE_SPESIFIKASI_MIGRASI_DATA.md`
- [ ] `docs/KRISA/D07_KRISA_ICTSERVE_PELAN_INTEGRASI_SISTEM.md`
- [ ] `docs/KRISA/D08_KRISA_ICTSERVE_SPESIFIKASI_INTEGRASI_DATA.md`
- [ ] `docs/KRISA/D09_KRISA_ICTSERVE_DOKUMENTASI_PANGKALAN_DATA.md`
- [ ] `docs/KRISA/D10_KRISA_ICTSERVE_DOKUMENTASI_KOD_SUMBER.md`
- [ ] `docs/KRISA/D15_KRISA_ICTSERVE_LAPORAN_MIGRASI_DATA.md`
- [ ] `docs/KRISA/D17_KRISA_ICTSERVE_MANUAL_PENGGUNA_SISTEM.md`
- [ ] `docs/KRISA/D17_KRISA_ICTSERVE_MANUAL_PENGGUNA_SISTEM_ADMIN.md`
- [ ] `docs/KRISA/diagram/KRISA_MERMAID_DIAGRAM_INDEX.md`
- [ ] `docs/KRISA/diagram/QUICK_SUITE_D03_MERMAID.md`

---

## 🔍 Semakan Pantas

Untuk setiap fail, jalankan:

```bash
# Semak emoji
findstr /N /C:"🔓" /C:"📝" /C:"🔐" /C:"📊" /C:"✅" /C:"📧" nama_fail.md

# Semak entiti HTML
findstr /N /C:"&quot;" /C:"&amp;" /C:"--&gt;" /C:"&lt;--&gt;" nama_fail.md

# Semak kurungan dalam label
findstr /N /C:"[" nama_fail.md | findstr /C:"("

# Semak arahan style dalam sequence diagram
findstr /N /C:"style " nama_fail.md
```

---

## 🛠️ Pembersihan Pantas

```powershell
# 1. Backup
copy nama_fail.md nama_fail.md.backup

# 2. Jalankan skrip automatik
.\clean-mermaid-diagrams.ps1 -FilePath "nama_fail.md"

# 3. Semak manual untuk:
#    - Kurungan dalam label
#    - Arahan style (sequence diagrams)
#    - Sintaks Mermaid

# 4. Sahkan dalam Mermaid Live Editor
#    https://mermaid.live/
```

---

## 📋 Penggantian Lazim

| Cari | Ganti |
|------|-------|
| `🔓` | (buang) |
| `📝` | (buang) |
| `🔐` | (buang) |
| `📊` | (buang) |
| `&quot;` | `"` |
| `&amp;` | `&` |
| `--&gt;` | `-->` |
| `&lt;--&gt;` | `<-->`|
| `(text)` | `- text` atau buang |
| `style X fill:#...` | (buang baris) |

---

## ✅ Pengesahan

- [ ] Backup dibuat
- [ ] Emoji dibuang
- [ ] Entiti HTML diperbaiki
- [ ] Kurungan dibuang/diganti
- [ ] Arahan style dibuang
- [ ] Sintaks disahkan
- [ ] Kandungan BM utuh
- [ ] Logik aliran kekal

---

**Kemaskini Terakhir**: 17 Disember 2025
