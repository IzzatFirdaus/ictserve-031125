# Prosedur Kecemasan AI Ollama ICTServe (ICTServe Ollama AI Emergency Procedures)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 12 Disember 2025  
**Status:** Aktif - Prosedur Kecemasan  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Pematuhi:** D11 v3.6.0 Technical Design Documentation

---

## Kontak Kecemasan (Emergency Contacts)

### Pasukan Respons Kecemasan (24/7)

| Peranan | Nama | Telefon Pejabat | Telefon Bimbit | E-mel | Waktu Respons |
|---------|------|----------------|----------------|-------|---------------|
| **Lead Developer** | Ahmad bin Ali | +603-8000-1234 | +6012-345-6789 | <ahmad.ali@motac.gov.my> | 15 minit |
| **System Administrator** | Siti binti Ahmad | +603-8000-1235 | +6013-456-7890 | <siti.ahmad@motac.gov.my> | 30 minit |
| **Database Administrator** | Muhammad bin Hassan | +603-8000-1236 | +6014-567-8901 | <muhammad.hassan@motac.gov.my> | 30 minit |
| **Network Administrator** | Fatimah binti Omar | +603-8000-1237 | +6015-678-9012 | <fatimah.omar@motac.gov.my> | 30 minit |

### Pengurusan Atasan

| Peranan | Nama | Telefon | E-mel | Untuk Eskalasi |
|---------|------|---------|-------|----------------|
| **Ketua Bahagian ICT** | Dato' Zainal bin Abdullah | +603-8000-1200 | <zainal.abdullah@motac.gov.my> | Tahap 3+ |
| **Pengarah BPM** | Datuk Aminah binti Ismail | +603-8000-1100 | <aminah.ismail@motac.gov.my> | Tahap 4+ |

---

## Tahap Kecemasan (Emergency Levels)

### Tahap 1: Masalah Kecil (Minor Issues)
**Kriteria:**

- Masa respons API > 5 saat tetapi < 10 saat
- Penggunaan CPU/memori 80-90%
- Beberapa ralat dalam log (< 10 dalam sejam)
- Cache hit rate < 80%

**Tindakan:**

- Hubungi System Administrator
- Pantau melalui Laravel Pulse dashboard
- Dokumentasikan dalam log sistem

**Masa Respons:** 30 minit

### Tahap 2: Masalah Sederhana (Moderate Issues)
**Kriteria:**

- Masa respons API > 10 saat
- Penggunaan CPU/memori > 90%
- Perkhidmatan terdegradasi tetapi masih boleh diakses
- Banyak ralat dalam log (10-50 dalam sejam)
- Ollama server tidak responsif

**Tindakan:**

- Hubungi Lead Developer dan System Administrator
- Aktifkan graceful degradation
- Restart perkhidmatan yang bermasalah
- Notifikasi pengguna melalui sistem

**Masa Respons:** 15 minit

### Tahap 3: Masalah Kritikal (Critical Issues)
**Kriteria:**

- API tidak boleh diakses sama sekali
- Database connection gagal
- Perkhidmatan utama down
- Ralat kritikal berterusan (> 50 dalam sejam)
- Keselamatan sistem terjejas

**Tindakan:**

- Hubungi semua ahli pasukan teknikal
- Aktifkan prosedur disaster recovery
- Pertimbangkan rollback ke versi stabil
- Notifikasi pengurusan atasan
- Dokumentasikan semua tindakan

**Masa Respons:** 5 minit

### Tahap 4: Bencana Sistem (System Disaster)
**Kriteria:**

- Kehilangan data kritikal
- Kerosakan perkakasan utama
- Serangan keselamatan yang berjaya
- Sistem tidak dapat dipulihkan dalam 4 jam

**Tindakan:**

- Hubungi semua kontak kecemasan
- Aktifkan disaster recovery site
- Hubungi vendor perkakasan/perisian
- Notifikasi pengurusan tertinggi
- Aktifkan protokol komunikasi krisis

**Masa Respons:** Segera

---

## Prosedur Respons Kecemasan

### 1. Prosedur Tahap 1: Masalah Kecil

#### Langkah 1: Penilaian Awal

```bash
# Jalankan health check
/usr/local/bin/health-check-ictserve-ai.sh

# Semak dashboard monitoring
# https://pulse.ictserve.motac.gov.my

# Semak log terkini
tail -100 /var/www/ictserve/storage/logs/laravel.log
```

#### Langkah 2: Tindakan Pembetulan

```bash
# Clear cache jika prestasi perlahan
cd /var/www/ictserve
php artisan cache:clear
php artisan config:cache

# Restart queue workers jika perlu
sudo supervisorctl restart ictserve-horizon

# Monitor untuk 15 minit
watch -n 60 'curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq .data.status'
```

#### Langkah 3: Dokumentasi

- Rekod masalah dalam log sistem
- Update status dalam Laravel Pulse
- Hantar laporan ringkas kepada Lead Developer

### 2. Prosedur Tahap 2: Masalah Sederhana

#### Langkah 1: Penilaian Segera

```bash
# Semak semua perkhidmatan
sudo systemctl status nginx mysql redis-server php8.2-fpm
sudo supervisorctl status

# Semak sumber sistem
htop
df -h
free -h
```

#### Langkah 2: Tindakan Pembetulan

```bash
# Restart perkhidmatan bermasalah
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart all

# Jika Ollama bermasalah
sudo systemctl restart ollama
ollama pull llama3.1:8b-instruct-q4_K_M  # Re-download model jika perlu

# Aktifkan graceful degradation
cd /var/www/ictserve
php artisan ai:enable-degradation --level=2
```

#### Langkah 3: Notifikasi Pengguna

```bash
# Hantar notifikasi sistem
php artisan notification:broadcast --type=system_maintenance --message="Sistem AI sedang dalam penyelenggaraan. Perkhidmatan mungkin perlahan."
```

### 3. Prosedur Tahap 3: Masalah Kritikal

#### Langkah 1: Penilaian Kritikal (5 minit)

```bash
# Semak status sistem secara menyeluruh
/usr/local/bin/health-check-ictserve-ai.sh

# Semak log ralat kritikal
journalctl --since "1 hour ago" --priority=crit

# Semak keselamatan sistem
last -n 20  # Semak login terkini
netstat -tulpn | grep :443  # Semak sambungan SSL
```

#### Langkah 2: Tindakan Pemulihan Segera

```bash
# Jika database bermasalah
sudo systemctl restart mysql
mysql -u root -p -e "SHOW PROCESSLIST;"  # Semak query yang berjalan

# Jika aplikasi bermasalah
cd /var/www/ictserve
git status  # Semak perubahan kod
php artisan down --message="Sistem dalam penyelenggaraan kecemasan"

# Pertimbangkan rollback
/usr/local/bin/rollback.sh --interactive
```

#### Langkah 3: Komunikasi Krisis

```bash
# Notifikasi pengguna
php artisan notification:broadcast --type=emergency --message="Sistem mengalami masalah teknikal. Pasukan teknikal sedang menyelesaikan masalah."

# Hantar alert kepada pengurusan
echo "ICTServe AI mengalami masalah kritikal. Pasukan teknikal sedang menangani." | mail -s "ALERT KRITIKAL: ICTServe AI" zainal.abdullah@motac.gov.my
```

### 4. Prosedur Tahap 4: Bencana Sistem

#### Langkah 1: Aktivasi Disaster Recovery

```bash
# Aktifkan disaster recovery site (jika ada)
# Hubungi penyedia hosting/cloud
# Mulakan pemulihan dari backup offsite

# Dokumentasikan semua tindakan
echo "$(date): Disaster recovery activated" >> /var/log/disaster-recovery.log
```

#### Langkah 2: Komunikasi Krisis

- Hubungi semua kontak kecemasan
- Notifikasi pengurusan tertinggi
- Sediakan kemas kini berkala kepada stakeholders
- Aktifkan saluran komunikasi alternatif

---

## Prosedur Pemulihan Khusus

### 1. Pemulihan Database Corruption

#### Gejala

- Error "Table doesn't exist" atau "Table is marked as crashed"
- Query database gagal dengan ralat InnoDB
- Aplikasi tidak dapat akses data

#### Tindakan Pemulihan

```bash
# Hentikan aplikasi
cd /var/www/ictserve
php artisan down

# Hentikan MySQL
sudo systemctl stop mysql

# Semak dan baiki jadual
sudo -u mysql myisamchk --check --force /var/lib/mysql/ictserve_production/*.MYI
sudo -u mysql myisamchk --recover --force /var/lib/mysql/ictserve_production/*.MYI

# Untuk InnoDB tables
sudo -u mysql innochecksum /var/lib/mysql/ictserve_production/users.ibd

# Mulakan MySQL
sudo systemctl start mysql

# Restore dari backup jika perlu
mysql -u root -p ictserve_production < /backup/ictserve-releases/database_backup_latest.sql

# Mulakan aplikasi
php artisan up
```

### 2. Pemulihan Ollama Server Crash

#### Gejala

- API mengembalikan "SERVICE_UNAVAILABLE"
- Ollama process tidak berjalan
- Model tidak dapat diakses

#### Tindakan Pemulihan

```bash
# Semak status Ollama
sudo systemctl status ollama
ps aux | grep ollama

# Restart Ollama service
sudo systemctl restart ollama

# Semak model yang tersedia
ollama list

# Re-download model jika perlu
ollama pull llama3.1:8b-instruct-q4_K_M

# Test model
ollama run llama3.1:8b-instruct-q4_K_M "Test message"

# Restart aplikasi services
sudo supervisorctl restart ictserve-horizon
```

### 3. Pemulihan Memory Exhaustion

#### Gejala

- "Out of memory" errors dalam log
- Aplikasi menjadi sangat perlahan
- Process killed oleh OOM killer

#### Tindakan Pemulihan

```bash
# Semak penggunaan memori
free -h
ps aux --sort=-%mem | head -20

# Restart perkhidmatan yang menggunakan memori tinggi
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart ictserve-horizon

# Tukar ke model AI yang lebih kecil sementara
ollama pull llama3.1:7b-instruct-q4_K_M

# Update konfigurasi sementara
cd /var/www/ictserve
sed -i 's/OLLAMA_MODEL=.*/OLLAMA_MODEL=llama3.1:7b-instruct-q4_K_M/' .env
php artisan config:cache

# Clear semua cache
php artisan cache:clear
redis-cli FLUSHALL
```

### 4. Pemulihan Network Connectivity Issues

#### Gejala

- API tidak boleh diakses dari luar
- WebSocket connections gagal
- SSL certificate errors

#### Tindakan Pemulihan

```bash
# Semak status network interfaces
ip addr show
netstat -tulpn | grep :443
netstat -tulpn | grep :6001

# Restart network services
sudo systemctl restart nginx
sudo systemctl restart networking

# Semak SSL certificate
openssl x509 -in /etc/ssl/certs/ictserve.motac.gov.my.crt -text -noout | grep "Not After"

# Test connectivity
curl -I https://ictserve.motac.gov.my
telnet ictserve.motac.gov.my 443

# Restart Reverb WebSocket server
sudo supervisorctl restart ictserve-reverb
```

---

## Prosedur Rollback Kecemasan

### Rollback Pantas (Quick Rollback)

#### Jika deployment terkini menyebabkan masalah

```bash
# Rollback automatik ke backup terkini
/usr/local/bin/rollback.sh --app-backup /backup/ictserve-releases/ictserve_backup_latest.tar.gz

# Atau rollback interaktif
/usr/local/bin/rollback.sh --interactive
```

#### Rollback Database Sahaja

```bash
# Jika hanya database bermasalah
cd /var/www/ictserve
php artisan down

# Restore database
mysql -u root -p ictserve_production < /backup/ictserve-releases/database_backup_latest.sql

# Run migrations jika perlu
php artisan migrate --force

php artisan up
```

### Rollback Konfigurasi

#### Jika konfigurasi baru menyebabkan masalah

```bash
cd /var/www/ictserve

# Restore .env dari backup
cp .env.backup.latest .env

# Clear dan rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Restart services
sudo systemctl restart php8.2-fpm nginx
sudo supervisorctl restart all
```

---

## Prosedur Komunikasi Krisis

### Template E-mel Kecemasan

#### Tahap 1-2: Masalah Teknikal

```
Subjek: [ICTServe AI] Masalah Teknikal - Tahap {LEVEL}

Kepada Semua,

Sistem ICTServe AI sedang mengalami masalah teknikal pada {TARIKH} jam {MASA}.

BUTIRAN MASALAH:
- Jenis: {JENIS_MASALAH}
- Kesan: {KESAN_KEPADA_PENGGUNA}
- Anggaran Pemulihan: {ANGGARAN_MASA}

TINDAKAN DIAMBIL:
- {TINDAKAN_1}
- {TINDAKAN_2}

STATUS SEMASA: {STATUS}

Kemas kini seterusnya akan dihantar dalam {MASA_KEMAS_KINI}.

Terima kasih,
Pasukan Teknikal ICTServe
```

#### Tahap 3-4: Kecemasan Kritikal

```
Subjek: [KECEMASAN KRITIKAL] ICTServe AI - Sistem Down

Kepada Pengurusan dan Pasukan Teknikal,

KECEMASAN KRITIKAL: Sistem ICTServe AI mengalami kegagalan sistem pada {TARIKH} jam {MASA}.

KESAN:
- Sistem tidak boleh diakses
- Semua perkhidmatan AI tergendala
- Pengguna tidak dapat mengakses FAQ Bot, analisis dokumen, atau auto-reply

TINDAKAN SEGERA:
- Pasukan kecemasan telah diaktifkan
- Prosedur disaster recovery sedang dilaksanakan
- Anggaran pemulihan: {ANGGARAN_MASA}

KONTAK KECEMASAN:
- Lead Developer: Ahmad bin Ali (+6012-345-6789)
- System Admin: Siti binti Ahmad (+6013-456-7890)

Kemas kini akan dihantar setiap 30 minit.

Pasukan Respons Kecemasan ICTServe
```

### Saluran Komunikasi

#### Saluran Utama

1. **E-mel**: Untuk dokumentasi formal dan eskalasi
2. **WhatsApp Group**: "ICTServe Emergency Response" untuk komunikasi pantas
3. **Microsoft Teams**: "ICTServe Technical Team" untuk koordinasi
4. **Telefon**: Untuk komunikasi segera dan kritikal

#### Protokol Komunikasi

- **Tahap 1-2**: E-mel dan Teams
- **Tahap 3**: E-mel, Teams, dan WhatsApp
- **Tahap 4**: Semua saluran termasuk panggilan telefon

---

## Prosedur Pemulihan Data

### 1. Pemulihan dari Backup Harian

```bash
#!/bin/bash
# restore-from-daily-backup.sh

BACKUP_DATE="$1"  # Format: YYYYMMDD
BACKUP_DIR="/backup/ictserve-releases"

if [[ -z "$BACKUP_DATE" ]]; then
    echo "Penggunaan: $0 YYYYMMDD"
    echo "Contoh: $0 20251211"
    exit 1
fi

# Find backup files for the specified date
APP_BACKUP=$(find "$BACKUP_DIR" -name "ictserve_backup_${BACKUP_DATE}_*.tar.gz" | head -1)
DB_BACKUP=$(find "$BACKUP_DIR" -name "database_backup_${BACKUP_DATE}_*.sql" | head -1)

if [[ -z "$APP_BACKUP" ]]; then
    echo "Backup aplikasi tidak dijumpai untuk tarikh: $BACKUP_DATE"
    exit 1
fi

echo "Backup dijumpai:"
echo "- Aplikasi: $APP_BACKUP"
echo "- Database: $DB_BACKUP"

read -p "Teruskan dengan pemulihan? (yes/no): " confirm
if [[ "$confirm" != "yes" ]]; then
    echo "Pemulihan dibatalkan"
    exit 0
fi

# Perform restoration
/usr/local/bin/rollback.sh --app-backup "$APP_BACKUP" --db-backup "$DB_BACKUP"
```

### 2. Pemulihan Selektif Data

```bash
#!/bin/bash
# selective-data-restore.sh

# Restore specific tables only
restore_table() {
    local table_name="$1"
    local backup_file="$2"
    
    echo "Memulihkan jadual: $table_name"
    
    # Extract specific table from backup
    sed -n "/^-- Table structure for table \`$table_name\`/,/^-- Table structure for table \`/p" "$backup_file" | head -n -1 > "/tmp/${table_name}_restore.sql"
    
    # Apply to database
    mysql -u root -p ictserve_production < "/tmp/${table_name}_restore.sql"
    
    # Cleanup
    rm -f "/tmp/${table_name}_restore.sql"
    
    echo "Jadual $table_name berjaya dipulihkan"
}

# Example usage
# restore_table "faqs" "/backup/database_backup_20251211_020000.sql"
# restore_table "documents" "/backup/database_backup_20251211_020000.sql"
```

---

## Prosedur Keselamatan Kecemasan

### 1. Respons Serangan Keselamatan

#### Jika serangan dikesan

```bash
# Segera block IP yang mencurigakan
sudo ufw insert 1 deny from {SUSPICIOUS_IP}

# Hentikan perkhidmatan web sementara
sudo systemctl stop nginx

# Semak log akses untuk aktiviti mencurigakan
tail -1000 /var/log/nginx/access.log | grep -E "(POST|PUT|DELETE)" | grep -v "200\|201\|204"

# Semak log aplikasi untuk ralat pengesahan
grep -i "authentication\|authorization\|failed\|unauthorized" /var/www/ictserve/storage/logs/laravel.log | tail -50

# Aktifkan mod keselamatan tinggi
cd /var/www/ictserve
php artisan down --secret=emergency-access-token-123

# Notifikasi pasukan keselamatan
echo "Serangan keselamatan dikesan pada ICTServe AI. Sistem dihentikan sementara." | mail -s "ALERT KESELAMATAN KRITIKAL" security@motac.gov.my
```

### 2. Pemulihan Selepas Serangan

```bash
# Selepas serangan diatasi
cd /var/www/ictserve

# Tukar semua kunci dan token
php artisan key:generate --force
php artisan sanctum:prune-expired --hours=0  # Clear all tokens

# Update password database
mysql -u root -p -e "UPDATE users SET password = NULL WHERE id > 0;" ictserve_production

# Force semua pengguna reset password
php artisan user:force-password-reset --all

# Scan untuk malware
sudo clamscan -r /var/www/ictserve --exclude-dir=vendor --exclude-dir=node_modules

# Mulakan semula dengan keselamatan dipertingkat
sudo systemctl start nginx
php artisan up
```

---

## Prosedur Disaster Recovery

### 1. Backup Offsite Recovery

```bash
#!/bin/bash
# disaster-recovery.sh

# Download backup from offsite location (AWS S3, etc.)
aws s3 cp s3://ictserve-disaster-backup/latest/ictserve_full_backup.tar.gz /tmp/

# Extract to temporary location
mkdir -p /tmp/ictserve-recovery
tar -xzf /tmp/ictserve_full_backup.tar.gz -C /tmp/ictserve-recovery

# Restore application
sudo rm -rf /var/www/ictserve
sudo mv /tmp/ictserve-recovery/ictserve /var/www/
sudo chown -R www-data:www-data /var/www/ictserve

# Restore database
mysql -u root -p ictserve_production < /tmp/ictserve-recovery/database_full_backup.sql

# Restart all services
sudo systemctl restart mysql nginx php8.2-fpm
sudo supervisorctl restart all
```

### 2. Alternative Site Activation

Jika pelayan utama tidak dapat dipulihkan:

1. **Aktifkan pelayan backup** (jika ada)
2. **Update DNS** untuk mengarah ke pelayan backup
3. **Restore data** dari backup terkini
4. **Notifikasi pengguna** tentang perubahan
5. **Monitor prestasi** pelayan backup

---

## Senarai Semak Pasca-Kecemasan

### Selepas Pemulihan Sistem

- [ ] **Sistem Berfungsi**: Semua perkhidmatan berjalan normal
- [ ] **Data Integrity**: Semua data penting utuh dan boleh diakses
- [ ] **Performance**: Masa respons dalam had normal
- [ ] **Security**: Tiada kelemahan keselamatan yang terbuka
- [ ] **Monitoring**: Semua sistem pemantauan aktif
- [ ] **Backup**: Backup terkini telah dibuat
- [ ] **Documentation**: Semua tindakan didokumentasikan
- [ ] **Notification**: Pengguna dimaklumkan sistem telah pulih
- [ ] **Post-Mortem**: Analisis punca masalah dijadualkan

### Laporan Pasca-Insiden

Template laporan yang mesti dilengkapkan dalam 24 jam:

```
LAPORAN PASCA-INSIDEN ICTSERVE AI

1. RINGKASAN INSIDEN
   - Tarikh/Masa: 
   - Tempoh: 
   - Tahap Kecemasan: 
   - Kesan: 

2. KRONOLOGI PERISTIWA
   - Masa deteksi: 
   - Tindakan pertama: 
   - Eskalasi: 
   - Pemulihan: 

3. PUNCA AKAR MASALAH
   - Punca utama: 
   - Faktor penyumbang: 
   - Analisis: 

4. TINDAKAN PEMBETULAN
   - Tindakan segera: 
   - Tindakan jangka panjang: 
   - Pencegahan: 

5. PENGAJARAN
   - Apa yang berjalan baik: 
   - Apa yang boleh diperbaiki: 
   - Cadangan penambahbaikan: 

Disediakan oleh: {NAMA}
Tarikh: {TARIKH}
```

---

**Dokumen ini mematuhi D11 v3.6.0 Technical Design Documentation dan menyediakan prosedur kecemasan yang komprehensif untuk sistem AI Ollama ICTServe.**
