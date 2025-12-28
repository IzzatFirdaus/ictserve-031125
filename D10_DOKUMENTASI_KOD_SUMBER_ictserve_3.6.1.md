# D10 DOKUMENTASI KOD SUMBER

**ICTServe**

| | |
| :--- | :--- |
| **NAMA AGENSI** | MOTAC |
| **NAMA AGENSI INDUK** | BPM MOTAC |
| **TARIKH DOKUMEN** | 29 Disember 2025 |
| **VERSI DOKUMEN** | 3.6.1 |

---

## i. Keterangan Dokumen
Dokumentasi kod sumber berdasarkan [_reference/versions/v3.6.1_D10_SOURCE_CODE_DOCUMENTATION.md](_reference/versions/v3.6.1_D10_SOURCE_CODE_DOCUMENTATION.md).

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
| 3.6.1 | 29/12/2025 | Struktur KRISA Kod Sumber | Pasukan BPM |

## iv. Kandungan
Rujuk seksyen 1–6.

## v. Senarai Gambarajah
- Peta modul (LR)

## vi. Senarai Jadual
- Jadual direktori & komponen

## vii. Definisi dan Akronim
| Akronim | Keterangan |
| :--- | :--- |
| MVC | Model-View-Controller |

## viii. Sumber Rujukan
- [docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/D10_TEMPLATE_DOKUMENTASI_KOD_SUMBER.md](docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/D10_TEMPLATE_DOKUMENTASI_KOD_SUMBER.md)

---

## 1. STRUKTUR KOD
```mermaid
graph LR
    app --> Models --> Services --> Livewire --> Filament
    resources --> views --> livewire
    routes --> web, api
```

## 2. KONVENSI
- PSR-12, strict types, Eloquent casts method

## 3. KOMPOSEN UTAMA
- Helpdesk, Pinjaman, Pentadbir

## 4. DEPENDENSI
- Laravel 12, Livewire 3, Filament 4, Redis, Reverb, Tailwind 4

## 5. UJIAN
- PHPUnit 11, Livewire tests

## 6. LAMPIRAN
- Senarai kelas & fail utama
