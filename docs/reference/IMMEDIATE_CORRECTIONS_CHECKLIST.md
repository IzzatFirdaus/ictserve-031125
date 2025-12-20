# Senarai Semak Pembetulan Segera - ICTServe Documentation

**Tarikh**: 20 Disember 2024  
**Keutamaan**: KRITIKAL  
**Anggaran Masa**: 2-4 jam

## ✅ Pembetulan Versi Teknologi (SEGERA)

### Versi Yang Perlu Dikemaskini

```diff
- Laravel Framework: 12.40.1 / 12.42.0
+ Laravel Framework: 12.43.1

- Livewire: 3.7.0 / 3.7.1  
+ Livewire: 3.7.3

- PHPUnit: 11.5.44
+ PHPUnit: 11.5.46

- Filament: 4.1.10
+ Filament: 4.3.1

- Laravel Horizon: TIDAK DIPASANG
+ Laravel Horizon: 5.41.0 (DIPASANG)
```

### Fail Yang Perlu Dikemaskini

- [ ] `docs/D01_PROJECT_OVERVIEW.md`
- [ ] `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md`
- [ ] `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md`
- [ ] `docs/D08_DEPLOYMENT_GUIDE.md`
- [ ] `docs/D11_TECHNICAL_DESIGN_DOCUMENTATION.md`
- [ ] `.kiro/steering/tech.md`
- [ ] `.kiro/steering/laravel-boost.md`
- [ ] `AGENTS.md`

## ✅ Pembetulan D17 Laravel Horizon (HARI INI)

### Masalah Semasa

```
Nama Fail: D17_QUEUE_MANAGEMENT_HORIZON.md
Kandungan: "Laravel Horizon tidak dipasang"
Realiti: Laravel Horizon v5.41.0 DIPASANG
```

### Tindakan

- [ ] Kemaskini kandungan D17 untuk mencerminkan pemasangan Horizon
- [ ] Tambah konfigurasi Horizon yang betul
- [ ] Kemaskini rujukan dalam D00 dan D18

## ✅ Pembetulan Konfigurasi Bahasa (MINGGU INI)

### D11 §8.1 - Konfigurasi Yang Salah

```diff
# SALAH (dalam dokumentasi)
- Default Locale: en
- Supported Locales: ['en', 'ms']

# BETUL (sepatutnya)
+ Default Locale: ms
+ Supported Locales: ['ms']
```

### Tindakan

- [ ] Kemaskini D11 §8.1 Internationalization
- [ ] Sahkan semua rujukan kepada locale dalam dokumentasi
- [ ] Pastikan konsisten dengan D15 "Bahasa Melayu Sahaja"

## ✅ Penyelarasan Queue Jobs (BULAN INI)

### Ketidakkonsistenan Senarai Job

**D00 vs D17**:

```diff
# D00 (Legacy naming)
- SendTicketCreatedEmail
- SendLoanApprovedEmail  
- SendAssetOverdueEmail

# D17 (Current naming)
+ SendTicketNotification
+ SendLoanNotification
+ SendApprovalRequest
```

### Tindakan

- [ ] Selaraskan penamaan job antara D00 dan D17
- [ ] Kemaskini dokumentasi job scheduling
- [ ] Sahkan nama kelas job dalam kod

## ✅ Pembersihan User Locale (BULAN INI)

### Konflik Status Kolum

```diff
# D15: Kolum users.locale DILUMPUHKAN
# D11: Sistem masih check users.locale Priority 1

# Penyelesaian:
- Hapuskan rujukan users.locale dari D11 §6.2
+ Kemaskini logic locale determination
```

### Tindakan

- [ ] Hapuskan rujukan `users.locale` dari D11
- [ ] Kemaskini dokumentasi user management
- [ ] Sahkan tiada kod bergantung kepada kolum ini

## 🔧 Skrip Automasi Pembetulan

### Cari & Ganti Versi (Bash)

```bash
#!/bin/bash
# update_versions.sh

# Laravel Framework
find docs/ -name "*.md" -exec sed -i 's/12\.40\.1/12.43.1/g' {} \;
find docs/ -name "*.md" -exec sed -i 's/12\.42\.0/12.43.1/g' {} \;

# Livewire  
find docs/ -name "*.md" -exec sed -i 's/3\.7\.0/3.7.3/g' {} \;
find docs/ -name "*.md" -exec sed -i 's/3\.7\.1/3.7.3/g' {} \;

# PHPUnit
find docs/ -name "*.md" -exec sed -i 's/11\.5\.44/11.5.46/g' {} \;

# Filament
find docs/ -name "*.md" -exec sed -i 's/4\.1\.10/4.3.1/g' {} \;

echo "✅ Versi dikemaskini dalam semua fail dokumentasi"
```

### Sahkan Perubahan

```bash
# Sahkan versi telah dikemaskini
grep -r "12.43.1" docs/
grep -r "3.7.3" docs/  
grep -r "11.5.46" docs/
grep -r "4.3.1" docs/
```

## 📋 Senarai Semak Kualiti

### Sebelum Commit

- [ ] Semua versi dikemaskini mengikut `application_info`
- [ ] D17 mencerminkan status Horizon yang betul
- [ ] Konfigurasi bahasa konsisten (ms sahaja)
- [ ] Tiada rujukan kepada `users.locale` yang dilumpuhkan
- [ ] Senarai queue jobs konsisten antara dokumen

### Selepas Commit  

- [ ] Jalankan `vendor/bin/pint` untuk formatting
- [ ] Sahkan tiada broken links dalam dokumentasi
- [ ] Test deployment menggunakan versi yang dikemaskini
- [ ] Kemaskini changelog dengan pembetulan

## 🚨 Amaran Penting

1. **Backup**: Buat backup dokumentasi sebelum pembetulan besar-besaran
2. **Testing**: Test semua rujukan versi dalam deployment scripts
3. **Communication**: Maklumkan kepada team tentang perubahan dokumentasi
4. **Validation**: Sahkan semua perubahan dengan `application_info` tool

## 📞 Sokongan

Jika ada masalah semasa pembetulan:

1. Rujuk kepada `composer.json` untuk versi sebenar
2. Gunakan `mcp_laravel_boost_application_info` untuk sahkan
3. Periksa `.kiro/steering/` files untuk panduan teknikal

---

**Status**: 🔄 DALAM PROSES  
**Kemaskini Terakhir**: 20 Disember 2024  
**Penanggung Jawab**: Tim Dokumentasi ICTServe
