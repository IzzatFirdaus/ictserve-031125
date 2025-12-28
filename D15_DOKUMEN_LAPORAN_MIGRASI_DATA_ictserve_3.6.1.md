# D15 DOKUMEN LAPORAN MIGRASI DATA

**ICTServe**

| | |
| :--- | :--- |
| **NAMA AGENSI** | MOTAC |
| **NAMA AGENSI INDUK** | BPM MOTAC |
| **TARIKH DOKUMEN** | 29 Disember 2025 |
| **VERSI DOKUMEN** | 3.6.1 |

---

## i. Keterangan Dokumen
Laporan migrasi data berdasarkan [_reference/versions/v3.6.1_D15_LANGUAGE_MS_EN.md](_reference/versions/v3.6.1_D15_LANGUAGE_MS_EN.md) dan D05/D06.

## ii. Semakan dan Pengesahan Dokumen

**SEMAKAN DOKUMEN**

| Disemak Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | | | |

**PENGESAHAN DOKUMEN**

| Disahkan Oleh | Jawatan | Tandatangan | Tarikh Semakan |
| :--- | :--- | :--- | :--- |
| | | | |

## iii. Kawalan Dokumen

| No. Versi | Tarikh | Ringkasan Pindaan | Penyedia |
| :--- | :--- | :--- | :--- |
| 3.6.1 | 29/12/2025 | Struktur KRISA Laporan Migrasi | Pasukan BPM |

## iv. Kandungan
Rujuk seksyen 1–6.

## v. Senarai Gambarajah
- Timeline migrasi (LR)

## vi. Senarai Jadual
- Jadual hasil migrasi

## vii. Definisi dan Akronim
| Akronim | Keterangan |
| :--- | :--- |
| ETL | Extract, Transform, Load |

## viii. Sumber Rujukan
- [docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/D15_TEMPLATE_LAPORAN_MIGRASI_DATA.md](docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/D15_TEMPLATE_LAPORAN_MIGRASI_DATA.md)

---

## 1. RINGKASAN MIGRASI
- Bilangan rekod berjaya vs gagal

## 2. METRIK

| Entiti | Berjaya | Gagal | Catatan |
| :--- | :--- | :--- | :--- |
| HelpdeskTicket |  |  |  |
| LoanApplication |  |  |  |

## 3. TIMELINE
```mermaid
graph LR
    P["Persediaan"] --> U["Ujian"] --> M["Pelaksanaan"] --> S["Semakan"]
```

## 4. ISU & PENYELESAIAN
- Validasi e-mel, trimming nama

## 5. RISIKO & MITIGASI
- Rollback automasi jika gagal

## 6. LAMPIRAN
- Log migrasi
