# Analisis Rekabentuk Halaman Utama ICTServe v3.6.0

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Kemaskini:** 13 Disember 2025  
**Status:** Aktif  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2

---

## Maklumat Dokumen (Document Information)

| Atribut | Nilai |
|---------|-------|
| **Versi** | 3.6.0 |
| **Tarikh Kemaskini** | 13 Disember 2025 |
| **Status** | Aktif |
| **Klasifikasi** | Terhad - Dalaman BPM MOTAC |
| **Tujuan** | Analisis rekabentuk halaman utama ICTServe berbanding spesifikasi D00-D17 dan panduan pembangunan frontend |
| **Skop** | Halaman utama (frontpage/landing page) sistem ICTServe |
| **Khalayak** | Pembangun frontend, penguji QA, agen AI, pengurus projek, pakar aksesibiliti |
| **Rujukan** | D00-D17, FRONTEND-DEVELOPMENT-v3-6-0.md |
| **Pematuhi** | ISO/IEC/IEEE 15288, ISO/IEC/IEEE 12207, WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, MyDS Design System v2025.2 |
| **Bahasa** | Bahasa Melayu (utama), English (teknikal) |

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh | Perubahan | Penulis |
|-------|--------|-----------|---------|
| 3.6.0 | 13 Disember 2025 | **Analisis awal**: Dokumentasi rekabentuk halaman utama ICTServe berbanding D00-D17 dan panduan frontend. Pengenalpastian isu pematuhan dan cadangan penambahbaikan. | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

- **[D00_SYSTEM_OVERVIEW.md]** - Ringkasan Sistem (v3.6.0)
- **[D12_UI_UX_DESIGN_GUIDE.md]** - Panduan Rekabentuk UI/UX
- **[D13_UI_UX_FRONTEND_FRAMEWORK.md]** - Framework Frontend UI/UX
- **[D14_UI_UX_STYLE_GUIDE.md]** - Panduan Gaya UI/UX
- **[D15_LANGUAGE_MS_EN.md]** - Panduan Bahasa (Bahasa Melayu sahaja, v3.6.0)
- **[docs/frontend/FRONTEND-DEVELOPMENT-v3-6-0.md]** - Panduan Pembangunan Frontend Komprehensif

---

## 1. Ringkasan Eksekutif (Executive Summary)

### 1.1. Status Semasa

Halaman utama ICTServe v3.6.0 menunjukkan implementasi **True Hybrid Architecture** dengan rekabentuk yang bersih dan profesional. Analisis ini membandingkan rekabentuk semasa dengan spesifikasi D00-D17 dan panduan pembangunan frontend untuk mengenal pasti kawasan penambahbaikan.

### 1.2. Penemuan Utama

**Kekuatan:**

- ✅ Pematuhan Bahasa Melayu sahaja (D15 v3.6.0)
- ✅ Struktur navigasi yang jelas
- ✅ Rekabentuk responsif
- ✅ Branding MOTAC yang konsisten

**Kawasan Penambahbaikan:**

- 🟡 Pematuhan WCAG 2.2 AA memerlukan pengesahan
- 🟡 Implementasi MyDS Design System v2025.2 tidak lengkap
- 🟡 Komponen aksesibiliti memerlukan penambahbaikan
- 🔴 Tiada dokumentasi ISO yang diperlukan (rujuk D00 §11)

---

## 2. Analisis Struktur Halaman (Page Structure Analysis)

### 2.1. Struktur HTML Semasa

Berdasarkan screenshot yang disediakan, halaman utama ICTServe mengandungi:

```html
<!DOCTYPE html>
<html lang="ms">
<head>
    <!-- Meta tags, title, CSS -->
</head>
<body>
    <!-- Header dengan navigasi (latar belakang biru) -->
    <header class="bg-blue-600">
        <nav>
            <!-- Logo ICTServe (putih) -->
            <!-- Menu: Admin ICT, Register Aset, Semak Status -->
            <!-- Butang: Guest, Dashboard -->
        </nav>
    </header>
    
    <!-- Hero section dengan latar belakang gelap -->
    <main>
        <section class="hero bg-dark">
            <!-- Logo MOTAC -->
            <!-- Tajuk: ICTServe (putih) -->
            <!-- Subtajuk: Sistem Perkhidmatan ICT (putih) -->
            <!-- Deskripsi platform (putih) -->
            <!-- CTA buttons: Buat Aduan, Mohon Pinjaman -->
            <!-- Input: Cari No. Rujukan -->
        </section>
        
        <!-- Services section (latar belakang terang) -->
        <section class="services bg-light">
            <!-- 3 kad perkhidmatan dengan latar belakang putih -->
            <!-- Aduan ICT, Pinjaman Aset, Semak Status -->
        </section>
        
        <!-- FAQ section dengan accordion -->
        <section class="faq">
            <!-- Soalan Lazim dengan accordion -->
        </section>
    </main>
    
    <!-- Footer -->
    <footer>
        <!-- Maklumat hubungan, pautan sosial -->
    </footer>
</body>
</html>
```

### 2.2. Analisis Mod Terang/Gelap (Light/Dark Mode Analysis)

Berdasarkan screenshot, halaman utama ICTServe menunjukkan implementasi **mod hibrid** dengan bahagian yang berbeza menggunakan skema warna yang berlainan:

#### 2.2.1. Bahagian Header (Mod Gelap)

- **Latar belakang**: Biru gelap (#0056B3 atau serupa)
- **Teks**: Putih untuk kontras optimum
- **Logo**: ICTServe dalam warna putih
- **Navigasi**: Menu dalam warna putih/terang

#### 2.2.2. Bahagian Hero (Mod Gelap)

- **Latar belakang**: Gelap dengan corak teknologi
- **Teks**: Putih untuk semua elemen teks
- **Logo MOTAC**: Dipaparkan dengan kontras yang sesuai
- **Butang CTA**: Kemungkinan menggunakan warna kontras (putih/terang)

#### 2.2.3. Bahagian Services (Mod Terang)

- **Latar belakang**: Terang/putih
- **Kad perkhidmatan**: Latar belakang putih dengan bayangan
- **Teks**: Gelap untuk kontras dengan latar belakang terang
- **Ikon**: Kemungkinan menggunakan warna primary (biru)

### 2.2. Pematuhan D00-D17

#### 2.2.1. D00 System Overview Compliance

| Keperluan D00 | Status Semasa | Cadangan |
|---------------|---------------|----------|
| **True Hybrid Architecture** | ✅ Dilaksanakan - Butang "Guest" dan "Dashboard" | Tambah penjelasan ringkas untuk pengguna baharu |
| **Bahasa Melayu sahaja (v3.6.0)** | ✅ Semua teks dalam BM | Kekalkan konsistensi |
| **Branding MOTAC** | ✅ Logo dan identiti visual | Pastikan pematuhan penuh dengan manual branding |

#### 2.2.2. D12 UI/UX Design Guide Compliance

| Keperluan D12 | Status Semasa | Isu | Cadangan |
|---------------|---------------|-----|----------|
| **WCAG 2.2 AA** | 🟡 Tidak disahkan | Kontras warna, focus indicators | Audit aksesibiliti penuh |
| **Touch targets ≥44×44px** | 🟡 Tidak disahkan | Saiz butang mobile | Pastikan minimum 44px |
| **Focus indicators** | 🔴 Tidak kelihatan | Tiada 3px outline | Tambah focus ring yang jelas |
| **Skip links** | 🔴 Tiada | Navigasi keyboard | Tambah "Langkau ke kandungan utama" |

#### 2.2.3. D13 Frontend Framework Compliance

| Keperluan D13 | Status Semasa | Isu | Cadangan |
|---------------|---------------|-----|----------|
| **MyDS Design System v2025.2** | 🟡 Sebahagian | Token warna, spacing tidak konsisten | Implementasi penuh MyDS tokens |
| **Tailwind CSS v4** | 🟡 Tidak disahkan | CSS framework version | Pastikan menggunakan v4.1.17 |
| **Component patterns** | 🟡 Tidak disahkan | Struktur komponen | Ikut corak Livewire/Volt |

#### 2.2.4. D15 Language Compliance

| Keperluan D15 | Status Semasa | Cadangan |
|---------------|---------------|----------|
| **Bahasa Melayu sahaja** | ✅ Dilaksanakan | Kekalkan konsistensi |
| **HTML lang attribute** | ✅ `lang="ms"` | Pastikan pada semua halaman |
| **Language switcher disabled** | ✅ Tiada penukar bahasa | Sesuai dengan v3.6.0 |

---

## 3. Analisis Komponen UI (UI Component Analysis)

### 3.1. Header/Navigation

**Status Semasa:**

- Logo ICTServe di sebelah kiri
- Menu navigasi: Admin ICT, Register Aset, Semak Status
- Butang aksi: Guest, Dashboard

**Isu yang Dikenal Pasti:**

1. **Aksesibiliti**: Tiada skip links untuk navigasi keyboard
2. **ARIA**: Menu mungkin memerlukan `role="navigation"` dan `aria-label`
3. **Focus management**: Tiada focus indicators yang jelas

**Cadangan Penambahbaikan:**

```html
<!-- Skip links (WCAG 2.4.1) -->
<a href="#main-content" class="skip-link">
    Langkau ke kandungan utama
</a>

<!-- Navigation dengan ARIA -->
<nav role="navigation" aria-label="Navigasi utama">
    <ul role="menubar">
        <li role="none">
            <a href="/admin-ict" role="menuitem" 
               class="focus:ring-2 focus:ring-primary-500">
                Admin ICT
            </a>
        </li>
        <!-- ... menu items lain -->
    </ul>
</nav>
```

### 3.2. Hero Section

**Status Semasa:**

- Logo MOTAC dengan latar belakang teknologi gelap
- Tajuk "ICTServe" dan subtajuk dalam warna putih
- Dua butang CTA utama
- Input carian rujukan
- **Skema Warna**: Mod gelap dengan teks putih

**Isu yang Dikenal Pasti:**

1. **Kontras**: Teks putih pada latar belakang gelap perlu diuji untuk memastikan 4.5:1 ratio
2. **Touch targets**: Butang mungkin kurang dari 44×44px pada mobile
3. **Form labels**: Input carian memerlukan label yang jelas
4. **Konsistensi mod**: Hero section menggunakan mod gelap manakala services section menggunakan mod terang

**Analisis Kontras Warna (Berdasarkan Screenshot):**

| Elemen | Warna Teks | Latar Belakang | Anggaran Kontras | Status |
|--------|------------|----------------|------------------|--------|
| Tajuk "ICTServe" | Putih (#FFFFFF) | Gelap (~#1a1a1a) | ~15:1 | ✅ Excellent |
| Subtajuk | Putih/Abu terang | Gelap | ~12:1 | ✅ Excellent |
| Deskripsi | Putih/Abu terang | Gelap | ~10:1 | ✅ Excellent |
| Butang CTA | Teks gelap | Latar terang | Perlu diuji | 🟡 Verify |

**Cadangan Penambahbaikan:**

```html
<section class="hero bg-linear-to-br from-gray-900 to-gray-800" role="banner">
    <div class="hero-content container mx-auto px-6 py-16 text-center">
        <img src="/logo-motac.png" 
             alt="Logo Kementerian Pelancongan, Seni dan Budaya Malaysia"
             class="mx-auto mb-6 h-20 w-auto" />
        
        <h1 class="text-4xl font-bold text-white mb-4 font-heading">
            ICTServe
        </h1>
        <h2 class="text-xl text-white/90 mb-2 font-body">
            Sistem Perkhidmatan ICT
        </h2>
        <p class="text-white/80 mb-8 max-w-2xl mx-auto font-body">
            Platform sehenti untuk aduan ICT dan permohonan pinjaman aset bagi warga kerja MOTAC
        </p>
        
        <!-- CTA buttons dengan touch targets yang sesuai -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="/helpdesk/create" 
               class="btn-primary min-h-11 px-6 py-3 bg-white text-primary-600 hover:bg-gray-50 rounded-m shadow-button font-medium transition-colors duration-200">
                Buat Aduan
            </a>
            <a href="/loan/create" 
               class="btn-secondary min-h-11 px-6 py-3 border-2 border-white text-white hover:bg-white hover:text-primary-600 rounded-m font-medium transition-colors duration-200">
                Mohon Pinjaman
            </a>
        </div>
        
        <!-- Search form dengan label yang jelas -->
        <form class="max-w-md mx-auto">
            <label for="reference-search" class="sr-only">
                Cari nombor rujukan tiket atau permohonan
            </label>
            <div class="flex">
                <input type="text" 
                       id="reference-search"
                       name="reference"
                       placeholder="Cari No. Rujukan (HCT-2024-000123)"
                       class="flex-1 px-4 py-3 rounded-l-m border-0 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                       aria-describedby="search-hint" />
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 text-white rounded-r-m hover:bg-primary-700 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-200"
                        aria-label="Cari rujukan">
                    Cari
                </button>
            </div>
            <p id="search-hint" class="text-sm text-white/70 mt-2 text-left">
                Masukkan nombor rujukan untuk menyemak status permohonan anda
            </p>
        </form>
    </div>
</section>
```

### 3.3. Services Cards Section

**Status Semasa (Berdasarkan Screenshot):**

- Tiga kad perkhidmatan dengan ikon dalam grid layout
- **Latar belakang section**: Terang/putih (mod terang)
- **Kad individual**: Latar belakang putih dengan bayangan
- **Teks**: Gelap untuk kontras dengan latar belakang terang
- Aduan ICT, Pinjaman Aset, Semak Status
- Butang tindakan di setiap kad

**Analisis Mod Terang (Light Mode):**

| Elemen | Warna | Kontras | Status |
|--------|-------|---------|--------|
| **Section Background** | Putih/Abu sangat terang | - | ✅ Clean |
| **Card Background** | Putih (#FFFFFF) | - | ✅ Clean |
| **Card Text** | Gelap (~#1a1a1a) | ~15:1 | ✅ Excellent |
| **Card Shadows** | Abu lembut | - | ✅ Subtle depth |
| **Icons** | Primary blue | 4.5:1+ | ✅ Good contrast |

**Isu yang Dikenal Pasti:**

1. **Semantic HTML**: Kad mungkin memerlukan struktur yang lebih semantik
2. **ARIA**: Perlu `role` dan `aria-label` yang sesuai
3. **Consistency**: Gaya butang mungkin tidak konsisten dengan MyDS
4. **Transition**: Peralihan dari hero section (gelap) ke services section (terang) perlu smooth

**Cadangan Penambahbaikan:**

```html
<section class="services py-16 bg-gray-50" id="main-content">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">
            Perkhidmatan Kami
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Aduan ICT Card -->
            <article class="service-card bg-white rounded-l shadow-card p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary-50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary-600" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Aduan ICT</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Laporkan kerosakan peralatan, sistem atau perkhidmatan ICT
                    </p>
                    <a href="/helpdesk/create" 
                       class="btn-primary w-full min-h-11 px-6 py-3 bg-primary-600 text-white rounded-m shadow-button hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
                       aria-describedby="aduan-desc">
                        Buat Aduan
                    </a>
                    <p id="aduan-desc" class="sr-only">
                        Buka borang untuk membuat aduan ICT baharu
                    </p>
                </div>
            </article>
            
            <!-- Pinjaman Aset Card -->
            <article class="service-card bg-white rounded-l shadow-card p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-success-50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-success-600" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Pinjaman Aset</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Mohon pinjaman aset ICT untuk keperluan kerja
                    </p>
                    <a href="/loan/create" 
                       class="btn-primary w-full min-h-11 px-6 py-3 bg-primary-600 text-white rounded-m shadow-button hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
                       aria-describedby="pinjaman-desc">
                        Mohon Pinjaman
                    </a>
                    <p id="pinjaman-desc" class="sr-only">
                        Buka borang untuk memohon pinjaman aset ICT
                    </p>
                </div>
            </article>
            
            <!-- Semak Status Card -->
            <article class="service-card bg-white rounded-l shadow-card p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-warning-50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-warning-600" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Semak Status</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Semak status aduan atau permohonan pinjaman anda
                    </p>
                    <a href="/status/check" 
                       class="btn-secondary w-full min-h-11 px-6 py-3 border-2 border-primary-600 text-primary-600 rounded-m hover:bg-primary-600 hover:text-white focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors duration-200"
                       aria-describedby="status-desc">
                        Semak Status
                    </a>
                    <p id="status-desc" class="sr-only">
                        Semak status aduan atau permohonan pinjaman anda
                    </p>
                </div>
            </article>
        </div>
    </div>
</section>
```

---

## 4. Analisis Pematuhan WCAG 2.2 AA (WCAG 2.2 AA Compliance Analysis)

### 4.1. Isu Kritikal yang Dikenal Pasti

| Kriteria WCAG | Isu | Tahap Keutamaan | Cadangan |
|---------------|-----|-----------------|----------|
| **1.4.3 Contrast (Minimum)** | Teks putih pada latar belakang mungkin <4.5:1 | 🔴 Kritikal | Uji kontras, laraskan warna |
| **2.4.1 Bypass Blocks** | Tiada skip links | 🔴 Kritikal | Tambah "Langkau ke kandungan utama" |
| **2.4.7 Focus Visible** | Tiada focus indicators | 🔴 Kritikal | Tambah 3px outline pada fokus |
| **1.3.1 Info and Relationships** | Label borang tidak jelas | 🟡 Sederhana | Tambah label tersembunyi untuk screen readers |
| **2.5.5 Target Size** | Butang mungkin <44×44px | 🟡 Sederhana | Pastikan minimum touch targets |

### 4.2. Senarai Semak Aksesibiliti (Accessibility Checklist)

#### 4.2.1. Perceivable (Dapat Dilihat)

- [ ] **Kontras warna**: Uji semua kombinasi teks/latar belakang
- [ ] **Teks alternatif**: Pastikan semua imej ada alt text yang bermakna
- [ ] **Struktur heading**: Gunakan h1-h6 secara hierarki
- [ ] **Landmark regions**: Tambah `<nav>`, `<main>`, `<aside>`, `<footer>`

#### 4.2.2. Operable (Boleh Dikendalikan)

- [ ] **Navigasi keyboard**: Semua elemen boleh diakses dengan Tab
- [ ] **Focus indicators**: 3px outline visible pada semua elemen interaktif
- [ ] **Touch targets**: Minimum 44×44px untuk mobile
- [ ] **Skip links**: "Langkau ke kandungan utama"

#### 4.2.3. Understandable (Dapat Difahami)

- [ ] **Bahasa halaman**: `<html lang="ms">`
- [ ] **Label borang**: Setiap input ada label yang jelas
- [ ] **Mesej ralat**: Jelas dan actionable
- [ ] **Arahan**: Mudah difahami

#### 4.2.4. Robust (Teguh)

- [ ] **HTML valid**: Lulus W3C validator
- [ ] **ARIA attributes**: Digunakan dengan betul
- [ ] **Semantic HTML**: Gunakan elemen yang sesuai

---

## 5. Analisis MyDS Design System v2025.2

### 5.1. Token System Implementation

**Status Semasa vs MyDS Requirements (Berdasarkan Screenshot):**

| MyDS Token | Keperluan | Status Semasa | Analisis Visual | Cadangan |
|------------|-----------|---------------|-----------------|----------|
| **Color Tokens** | Primary #0056B3, Success #1B7C54 | 🟡 Sebahagian | Header menggunakan biru gelap, services section terang | Audit warna, pastikan pematuhan penuh |
| **Radius Tokens** | xs(4px), s(6px), m(8px), l(12px) | 🟡 Tidak konsisten | Kad services kelihatan menggunakan radius sederhana | Gunakan `rounded-m` untuk butang, `rounded-l` untuk kad |
| **Shadow Tokens** | button, card, dropdown | � Diilaksanakan | Kad services menunjukkan bayangan yang sesuai | Pastikan konsistensi `shadow-button`, `shadow-card` |
| **Spacing Tokens** | space-1(4px) hingga space-16(64px) | 🟡 Tidak konsisten | Jarak antara elemen kelihatan seragam | Gunakan `gap-4`, `p-6` mengikut MyDS |
| **Typography** | Poppins (headings), Inter (body) | � Tidakk disahkan | Font kelihatan konsisten tetapi perlu pengesahan | Pastikan font loading Poppins/Inter |

### 5.2. Analisis Skema Warna Hibrid (Hybrid Color Scheme Analysis)

**Berdasarkan Screenshot:**

#### 5.2.1. Header Section (Dark Theme)

```css
/* Header - Dark Mode */
.header {
    background: var(--color-primary-600); /* ~#0056B3 */
    color: var(--txt-white);
}

.header-logo {
    color: var(--txt-white);
}

.header-nav {
    color: var(--txt-white);
}
```

#### 5.2.2. Hero Section (Dark Theme)

```css
/* Hero - Dark Mode */
.hero {
    background: linear-gradient(135deg, var(--color-gray-900), var(--color-gray-800));
    color: var(--txt-white);
}

.hero-title {
    color: var(--txt-white);
    font-family: var(--font-heading); /* Poppins */
}

.hero-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-family: var(--font-body); /* Inter */
}
```

#### 5.2.3. Services Section (Light Theme)

```css
/* Services - Light Mode */
.services {
    background: var(--bg-washed); /* Light gray */
}

.service-card {
    background: var(--bg-white);
    box-shadow: var(--shadow-card);
    border-radius: var(--radius-l); /* 12px */
}

.service-card h3 {
    color: var(--txt-black-900);
    font-family: var(--font-heading);
}

.service-card p {
    color: var(--txt-black-700);
    font-family: var(--font-body);
}
```

### 5.2. Component Patterns

**Butang (Buttons):**

```css
/* Semasa (perlu pengesahan) */
.btn-primary {
    /* Gaya tidak diketahui */
}

/* MyDS Compliant */
.btn-primary {
    min-height: 44px; /* Touch target */
    padding: 12px 24px;
    border-radius: var(--radius-m); /* 8px */
    box-shadow: var(--shadow-button);
    font-family: var(--font-body); /* Inter */
    font-weight: 500;
    transition: var(--duration-short) var(--motion-easeout);
}
```

**Kad (Cards):**

```css
/* MyDS Compliant */
.service-card {
    border-radius: var(--radius-l); /* 12px */
    box-shadow: var(--shadow-card);
    padding: var(--space-6); /* 24px */
    background: var(--bg-white);
}
```

---

## 6. Cadangan Penambahbaikan Keutamaan (Priority Improvement Recommendations)

### 6.1. Keutamaan P0 (Kritikal - Mesti Diperbaiki)

#### 6.1.1. Aksesibiliti WCAG 2.2 AA

1. **Skip Links**

   ```html
   <a href="#main-content" class="skip-link sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-white text-primary-600 px-4 py-2 rounded-m shadow-lg z-50">
       Langkau ke kandungan utama
   </a>
   ```

2. **Focus Indicators (Global)**

   ```css
   /* Global focus ring untuk semua elemen interaktif */
   :focus-visible {
       outline: 3px solid var(--fr-primary);
       outline-offset: 2px;
   }
   
   /* Focus ring khusus untuk butang pada latar belakang gelap */
   .hero :focus-visible {
       outline-color: var(--color-white);
       outline-offset: 2px;
   }
   ```

3. **Kontras Warna (Berdasarkan Analisis Screenshot)**

   **Audit Diperlukan:**
   - ✅ Header: Teks putih pada biru gelap (kontras excellent)
   - ✅ Hero: Teks putih pada latar gelap (kontras excellent)  
   - ✅ Services: Teks gelap pada latar terang (kontras excellent)
   - 🟡 Butang CTA: Perlu diuji kontras teks pada butang
   - 🟡 Input placeholder: Perlu diuji kontras placeholder text

4. **Touch Targets**

   ```css
   /* Pastikan semua elemen interaktif minimum 44x44px */
   .btn, .link, .input, button, a[href] {
       min-height: 44px;
       min-width: 44px;
   }
   
   /* Khusus untuk mobile */
   @media (max-width: 768px) {
       .btn, .service-card a {
           min-height: 48px; /* Sedikit lebih besar untuk mobile */
           padding: 12px 16px;
       }
   }
   ```

#### 6.1.2. Konsistensi Skema Warna Hibrid

**Masalah**: Peralihan mendadak dari hero section (gelap) ke services section (terang)

**Penyelesaian**:

```css
/* Tambah gradient transition antara sections */
.hero-to-services-transition {
    background: linear-gradient(to bottom, 
        var(--color-gray-800) 0%,
        var(--color-gray-100) 100%);
    height: 80px;
}

/* Atau gunakan subtle border */
.services {
    border-top: 1px solid var(--otl-divider);
}
```

#### 6.1.2. Dokumentasi ISO (D00 §11)

Tambah pengecam dokumen ISO yang diperlukan:

```html
<!-- Header dengan ID dokumen -->
<div class="iso-document-id">
    PK.(S).MOTAC.07.(L1) - ICTServe Portal
</div>
```

### 6.2. Keutamaan P1 (Tinggi - Patut Diperbaiki)

#### 6.2.1. MyDS Design System Implementation Penuh

1. **Color Tokens (Berdasarkan Analisis Visual)**

   ```css
   :root {
       /* Primary colors (kelihatan digunakan dalam header) */
       --color-primary-500: #0056B3;
       --color-primary-600: #004494; /* Header background */
       --color-primary-700: #003875;
       
       /* Semantic colors untuk services cards */
       --color-success-500: #1B7C54;
       --color-warning-500: #CC7700;
       --color-danger-500: #B3002D;
       
       /* Background tokens (berdasarkan screenshot) */
       --bg-white: #FFFFFF; /* Services cards */
       --bg-washed: #F9FAFB; /* Services section */
       --bg-dark: #1F2937; /* Hero section */
   }
   ```

2. **Typography System (Perlu Pengesahan)**

   ```css
   @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');
   
   :root {
       --font-heading: 'Poppins', system-ui, sans-serif;
       --font-body: 'Inter', system-ui, sans-serif;
   }
   
   /* Aplikasi pada elemen */
   .hero h1, .services h2, .service-card h3 {
       font-family: var(--font-heading);
   }
   
   .hero p, .service-card p, body {
       font-family: var(--font-body);
   }
   ```

3. **Component Standardization (Berdasarkan Screenshot)**

   ```css
   /* Service cards - kelihatan menggunakan shadow yang sesuai */
   .service-card {
       background: var(--bg-white);
       border-radius: var(--radius-l); /* 12px */
       box-shadow: var(--shadow-card);
       padding: var(--space-6); /* 24px */
       transition: box-shadow var(--duration-short) var(--motion-easeout);
   }
   
   .service-card:hover {
       box-shadow: var(--shadow-dropdown); /* Elevated shadow */
   }
   
   /* Buttons - perlu standardisasi */
   .btn-primary {
       border-radius: var(--radius-m); /* 8px */
       box-shadow: var(--shadow-button);
       padding: var(--space-3) var(--space-6); /* 12px 24px */
       min-height: 44px;
   }
   ```

#### 6.2.2. Theme Switcher Integration (v3.6.0)

**Berdasarkan D12 v3.6.0**, sistem kini menyokong Theme Switcher (mod terang/gelap):

```html
<!-- Theme Switcher Component -->
<livewire:components.theme-toggle />
```

**Implementasi untuk Frontpage:**

```css
/* Light mode (default) */
:root {
    --hero-bg: var(--color-gray-900);
    --hero-text: var(--color-white);
    --services-bg: var(--bg-washed);
    --services-text: var(--txt-black-900);
}

/* Dark mode */
[data-theme="dark"] {
    --hero-bg: var(--color-gray-800);
    --hero-text: var(--color-white);
    --services-bg: var(--color-gray-900);
    --services-text: var(--color-white);
}

/* Services cards dalam dark mode */
[data-theme="dark"] .service-card {
    background: var(--color-gray-800);
    color: var(--color-white);
    border: 1px solid var(--color-gray-700);
}
```

#### 6.2.2. Semantic HTML Structure

```html
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICTServe - Sistem Perkhidmatan ICT MOTAC</title>
</head>
<body>
    <!-- Skip links -->
    <a href="#main-content" class="skip-link">Langkau ke kandungan utama</a>
    
    <!-- Header -->
    <header role="banner">
        <nav role="navigation" aria-label="Navigasi utama">
            <!-- Navigation content -->
        </nav>
    </header>
    
    <!-- Main content -->
    <main id="main-content" tabindex="-1">
        <!-- Hero section -->
        <section role="banner" class="hero">
            <!-- Hero content -->
        </section>
        
        <!-- Services -->
        <section aria-labelledby="services-heading">
            <h2 id="services-heading">Perkhidmatan Kami</h2>
            <!-- Services content -->
        </section>
        
        <!-- FAQ -->
        <section aria-labelledby="faq-heading">
            <h2 id="faq-heading">Soalan Lazim</h2>
            <!-- FAQ content -->
        </section>
    </main>
    
    <!-- Footer -->
    <footer role="contentinfo">
        <!-- Footer content -->
    </footer>
</body>
</html>
```

### 6.3. Keutamaan P2 (Sederhana - Boleh Diperbaiki Kemudian)

1. **Performance Optimization**
   - Lazy loading untuk imej
   - CSS/JS minification
   - Web font optimization

2. **Enhanced UX**
   - Loading states untuk butang
   - Hover effects yang konsisten
   - Micro-interactions

3. **Advanced Accessibility**
   - High contrast mode support
   - Reduced motion preferences
   - Screen reader testing

---

## 7. Rancangan Pelaksanaan (Implementation Plan)

### 7.1. Fasa 1: Pematuhan Kritikal (1-2 minggu)

**Minggu 1:**

- [ ] Tambah skip links
- [ ] Implementasi focus indicators
- [ ] Audit dan perbaiki kontras warna
- [ ] Pastikan touch targets minimum 44×44px

**Minggu 2:**

- [ ] Tambah dokumentasi ISO yang diperlukan
- [ ] Perbaiki struktur HTML semantic
- [ ] Tambah ARIA labels dan roles
- [ ] Ujian aksesibiliti dengan screen reader

### 7.2. Fasa 2: MyDS Implementation (2-3 minggu)

**Minggu 3-4:**

- [ ] Implementasi MyDS color tokens
- [ ] Setup typography system (Poppins/Inter)
- [ ] Standardkan component patterns
- [ ] Implementasi shadow system

**Minggu 5:**

- [ ] Ujian cross-browser
- [ ] Performance optimization
- [ ] Documentation updates

### 7.3. Fasa 3: Enhancement (1-2 minggu)

**Minggu 6-7:**

- [ ] Advanced accessibility features
- [ ] UX enhancements
- [ ] Final testing dan validation

---

## 8. Metrik Kejayaan (Success Metrics)

### 8.1. Aksesibiliti

| Metrik | Sasaran | Kaedah Pengukuran | Status Semasa |
|--------|---------|-------------------|---------------|
| **Lighthouse Accessibility Score** | ≥95 | Automated audit | 🟡 Perlu diuji |
| **WCAG 2.2 AA Violations** | 0 | axe DevTools | 🟡 Perlu audit |
| **Keyboard Navigation** | 100% functional | Manual testing | 🟡 Perlu diuji |
| **Screen Reader Compatibility** | 100% readable | NVDA/JAWS testing | 🟡 Perlu diuji |
| **Color Contrast** | 4.5:1 (text), 3:1 (UI) | Contrast checker | 🟢 Kelihatan baik |
| **Touch Targets** | ≥44×44px | Manual measurement | 🟡 Perlu diuji |

### 8.2. Performance

| Metrik | Sasaran | Kaedah Pengukuran |
|--------|---------|-------------------|
| **Lighthouse Performance** | ≥90 | Automated audit |
| **First Contentful Paint** | <1.5s | Web Vitals |
| **Largest Contentful Paint** | <2.5s | Web Vitals |
| **Cumulative Layout Shift** | <0.1 | Web Vitals |

### 8.3. Pematuhan

| Metrik | Sasaran | Kaedah Pengukuran |
|--------|---------|-------------------|
| **MyDS Token Usage** | 100% | Code review |
| **HTML Validation** | 0 errors | W3C Validator |
| **CSS Validation** | 0 errors | W3C CSS Validator |
| **D00-D17 Compliance** | 100% | Documentation review |

---

## 9. Kesimpulan dan Langkah Seterusnya (Conclusion and Next Steps)

### 9.1. Ringkasan Penemuan

Halaman utama ICTServe v3.6.0 menunjukkan asas yang baik untuk True Hybrid Architecture dengan rekabentuk hibrid yang menarik (kombinasi mod gelap dan terang). Berdasarkan screenshot yang disediakan, sistem menunjukkan implementasi visual yang profesional dengan beberapa kawasan yang memerlukan penambahbaikan untuk pematuhan penuh.

**Kekuatan Utama:**

- ✅ **Skema warna hibrid yang menarik**: Header dan hero section (gelap) dengan services section (terang)
- ✅ **Struktur navigasi yang jelas**: Header dengan logo dan menu yang mudah difahami
- ✅ **Pematuhan bahasa Melayu sahaja** (D15 v3.6.0): Semua teks dalam Bahasa Melayu
- ✅ **Layout grid yang teratur**: Services cards dalam grid 3 kolom yang seimbang
- ✅ **Branding MOTAC yang konsisten**: Logo dan identiti visual yang seragam
- ✅ **Kontras warna yang baik**: Teks putih pada latar gelap dan teks gelap pada latar terang
- ✅ **Bayangan kad yang sesuai**: Services cards menggunakan shadow yang memberikan depth

**Kawasan Penambahbaikan Kritikal:**

- 🟡 **Pematuhan aksesibiliti WCAG 2.2 AA**: Perlu audit menyeluruh untuk skip links, focus indicators, dan touch targets
- 🟡 **Implementasi MyDS Design System v2025.2**: Perlu pengesahan penggunaan token yang betul
- 🟡 **Semantic HTML structure**: Perlu penambahbaikan struktur HTML untuk screen readers
- 🟡 **Peralihan section**: Peralihan dari hero (gelap) ke services (terang) boleh diperbaiki
- 🔴 **Dokumentasi ISO yang diperlukan**: Tiada ID dokumen ISO yang kelihatan

**Penemuan Baharu (Berdasarkan Screenshot):**

- **Mod hibrid**: Sistem menggunakan kombinasi dark/light mode yang menarik
- **Konsistensi visual**: Warna, spacing, dan typography kelihatan konsisten
- **Professional appearance**: Keseluruhan rekabentuk kelihatan profesional dan sesuai untuk kegunaan kerajaan

### 9.2. Tindakan Segera Diperlukan

1. **Audit Aksesibiliti Menyeluruh**:
   - Jalankan ujian WCAG 2.2 AA menggunakan axe DevTools dan Lighthouse
   - Uji navigasi keyboard pada semua elemen interaktif
   - Uji dengan screen reader (NVDA/JAWS) untuk memastikan semua kandungan boleh dibaca

2. **Implementasi Skip Links**:
   - Tambah "Langkau ke kandungan utama" di bahagian atas halaman
   - Pastikan skip link kelihatan apabila mendapat fokus

3. **Focus Management**:
   - Pastikan semua butang, pautan, dan input ada focus indicators yang jelas
   - Khusus untuk hero section (latar gelap), gunakan focus ring putih
   - Untuk services section (latar terang), gunakan focus ring biru

4. **Kontras Warna (Berdasarkan Screenshot)**:
   - ✅ Header: Kontras teks putih pada biru gelap sudah baik
   - ✅ Hero: Kontras teks putih pada latar gelap sudah baik  
   - ✅ Services: Kontras teks gelap pada latar terang sudah baik
   - 🟡 Perlu uji kontras pada butang CTA dan placeholder text

5. **Theme Switcher Integration**:
   - Implementasi theme switcher mengikut D12 v3.6.0
   - Pastikan kedua-dua mod (terang/gelap) mematuhi kontras WCAG
   - Simpan pilihan pengguna dalam localStorage

### 9.3. Langkah Seterusnya

1. **Fasa Pelaksanaan**:
   - Ikut rancangan 3 fasa yang dicadangkan (P0 → P1 → P2)
   - Fokus pada P0 (aksesibiliti kritikal) dahulu
   - Kemudian P1 (MyDS implementation dan theme switcher)

2. **Ujian Berterusan**:
   - Setup automated testing untuk aksesibiliti (Playwright + axe)
   - Implementasi performance monitoring dengan Laravel Pulse
   - Setup CI/CD pipeline untuk WCAG compliance checking

3. **Dokumentasi**:
   - Kemaskini dokumentasi D00-D17 dengan perubahan yang dibuat
   - Dokumentasi theme switcher implementation dalam D12
   - Update frontend development guide dengan hybrid color scheme patterns

4. **Training**:
   - Latih pasukan pembangunan tentang MyDS Design System v2025.2
   - Workshop WCAG 2.2 AA compliance untuk frontend developers
   - Panduan penggunaan theme switcher dan hybrid color schemes

5. **Monitoring dan Feedback**:
   - Setup user feedback collection untuk theme preferences
   - Monitor Core Web Vitals untuk kedua-dua light dan dark modes
   - Track accessibility metrics melalui automated tools

### 9.4. Pemantauan dan Penyelenggaraan

- **Audit Bulanan**: Lighthouse dan axe DevTools
- **Ujian Pengguna**: UAT dengan pengguna sebenar setiap suku tahun
- **Performance Monitoring**: Setup Laravel Pulse untuk pemantauan berterusan
- **Compliance Review**: Semakan pematuhan D00-D17 setiap 6 bulan

---

## Lampiran (Appendices)

### A. Senarai Semak WCAG 2.2 AA Lengkap

| Kriteria | Deskripsi | Status | Tindakan Diperlukan |
|----------|-----------|--------|---------------------|
| 1.1.1 Non-text Content | Alt text untuk imej | 🟡 | Audit semua imej |
| 1.3.1 Info and Relationships | Struktur semantic | 🔴 | Tambah ARIA labels |
| 1.4.3 Contrast (Minimum) | Kontras 4.5:1 | 🔴 | Audit warna |
| 2.1.1 Keyboard | Akses keyboard | 🟡 | Ujian navigasi |
| 2.4.1 Bypass Blocks | Skip links | 🔴 | Tambah skip links |
| 2.4.7 Focus Visible | Focus indicators | 🔴 | Tambah focus ring |
| 2.5.5 Target Size | Touch targets 44×44px | 🟡 | Audit saiz butang |

### B. MyDS Token Reference

```css
/* Color Tokens */
--color-primary-500: #0056B3;
--color-success-500: #1B7C54;
--color-danger-500: #B3002D;

/* Spacing Tokens */
--space-4: 16px;
--space-6: 24px;
--space-8: 32px;

/* Radius Tokens */
--radius-m: 8px;
--radius-l: 12px;

/* Shadow Tokens */
--shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07);
--shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05);
```

### C. Contoh Kod Komponen

```blade
{{-- Hero Section dengan WCAG compliance --}}
<section class="hero bg-linear-to-r from-primary-600 to-primary-800" role="banner">
    <div class="container mx-auto px-6 py-16 text-center">
        <img src="/logo-motac.png" 
             alt="Logo Kementerian Pelancongan, Seni dan Budaya Malaysia"
             class="mx-auto mb-6 h-20 w-auto" />
        
        <h1 class="text-4xl font-bold text-white mb-4 font-heading">
            ICTServe
        </h1>
        
        <h2 class="text-xl text-white/90 mb-2 font-body">
            Sistem Perkhidmatan ICT
        </h2>
        
        <p class="text-white/80 mb-8 max-w-2xl mx-auto font-body">
            Platform sehenti untuk aduan ICT dan permohonan pinjaman aset bagi warga kerja MOTAC
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="/helpdesk/create" 
               class="btn-primary min-h-11 px-6 py-3 bg-white text-primary-600 hover:bg-gray-50 rounded-m shadow-button font-medium transition-colors duration-200 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600">
                Buat Aduan
            </a>
            <a href="/loan/create" 
               class="btn-secondary min-h-11 px-6 py-3 border-2 border-white text-white hover:bg-white hover:text-primary-600 rounded-m font-medium transition-colors duration-200 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600">
                Mohon Pinjaman
            </a>
        </div>
        
        <form class="max-w-md mx-auto">
            <label for="reference-search" class="sr-only">
                Cari nombor rujukan tiket atau permohonan
            </label>
            <div class="flex">
                <input type="text" 
                       id="reference-search"
                       name="reference"
                       placeholder="Cari No. Rujukan (HCT-2024-000123)"
                       class="flex-1 px-4 py-3 rounded-l-m border-0 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600"
                       aria-describedby="search-hint" />
                <button type="submit" 
                        class="px-6 py-3 bg-secondary-600 text-white rounded-r-m hover:bg-secondary-700 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600 transition-colors duration-200"
                        aria-label="Cari rujukan">
                    Cari
                </button>
            </div>
            <p id="search-hint" class="text-sm text-white/70 mt-2 text-left">
                Masukkan nombor rujukan untuk menyemak status permohonan anda
            </p>
        </form>
    </div>
</section>
```

---

**Status Dokumen:** ✅ Aktif  
**Semakan Seterusnya:** 13 Mac 2026  
**Diselenggara Oleh:** Pasukan Pembangunan Frontend  
**Diluluskan Oleh:** Jawatankuasa Seni Bina Teknikal  
**Pematuhan:** D00-D17 v3.6.0, MyDS Design System v2025.2, WCAG 2.2 AA
