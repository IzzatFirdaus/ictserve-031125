# Panduan Migrasi ICTServe v3.6.0

**Tarikh**: 9 Disember 2025  
**Versi**: 3.6.0

---

## Ringkasan Perubahan

ICTServe v3.6.0 memperkenalkan perubahan penting kepada antara muka pengguna:

1. **Bahasa Melayu Sahaja**: Antara muka kini menggunakan Bahasa Melayu sahaja
2. **Theme Switcher**: Pilihan mod gelap (dark mode) baharu dengan mod terang sebagai lalai

---

## Perubahan Bahasa (Language Changes)

### Sebelum (v3.5.0)

- Antara muka dwibahasa (Bahasa Melayu + Bahasa Inggeris)
- Penukar bahasa (language switcher) tersedia di semua halaman
- Pengguna boleh memilih bahasa pilihan mereka

### Selepas (v3.6.0)

- Antara muka Bahasa Melayu sahaja
- Penukar bahasa telah dikeluarkan
- Semua kandungan dipaparkan dalam Bahasa Melayu

### Kesan kepada Pengguna

- Pengguna yang sebelum ini menggunakan Bahasa Inggeris akan melihat antara muka dalam Bahasa Melayu
- Tiada tindakan diperlukan daripada pengguna
- Fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal

---

## Komponen Dilumpuhkan

| Komponen | Status | Penggantian |
|----------|--------|-------------|
| LanguageSwitcher | Dipadam | Tiada (Bahasa Melayu sahaja) |
| BilingualSupportService | Dilumpuhkan | Mengembalikan 'ms' sahaja |
| SetLocale middleware | Dilumpuhkan | Sentiasa 'ms' |
| users.locale column | Dilumpuhkan | Sentiasa 'ms' |
| ictserve_locale cookie | Dipadam | Tiada |

---

## Theme Switcher (Ciri Baharu)

### Fungsi

- Pilihan mod terang (light) atau mod gelap (dark)
- Mod terang adalah lalai untuk semua pengguna baharu
- Pilihan disimpan dalam localStorage pelayar

### Lokasi

- Butang penukar tema tersedia di bahagian atas kanan semua halaman
- Ikon matahari (☀️) untuk mod terang, ikon bulan (🌙) untuk mod gelap

---

## Soalan Lazim (FAQ)

### S: Mengapa penukar bahasa dikeluarkan?
J: Untuk memudahkan penyelenggaraan dan memastikan konsistensi antara muka, sistem kini menggunakan Bahasa Melayu sahaja selaras dengan dasar kerajaan.

### S: Bolehkah saya masih menggunakan Bahasa Inggeris?
J: Tidak, antara muka kini dalam Bahasa Melayu sahaja. Walau bagaimanapun, fail terjemahan Bahasa Inggeris dikekalkan untuk rujukan teknikal.

### S: Bagaimana untuk menukar tema?
J: Klik butang penukar tema di bahagian atas kanan halaman dan pilih "Terang" atau "Gelap".

---

## Hubungi Kami

Untuk sebarang pertanyaan mengenai migrasi v3.6.0, sila hubungi:

- E-mel: <ict@motac.gov.my>
- Helpdesk: Gunakan borang helpdesk ICTServe
