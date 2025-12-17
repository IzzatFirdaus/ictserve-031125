# Rancangan Kemaskini Frontend Pengguna Disahkan (Authenticated Frontend Update Plan)

**Sistem ICTServe**  
**Versi:** 3.6.0 (SemVer)  
**Tarikh Dicipta:** 13 Disember 2025  
**Status:** Aktif - Perancangan  
**Klasifikasi:** Terhad - Dalaman BPM MOTAC  
**Penulis:** Pasukan Pembangunan BPM MOTAC  
**Standard Rujukan:** WCAG 2.2 AA, MyDS Design System v2025.2, D00-D17 v3.6.0

---

## 1. Ringkasan Eksekutif (Executive Summary)

### 1.1. Tujuan Dokumen

Dokumen ini menyediakan rancangan komprehensif untuk mengemaskini semua komponen frontend pengguna disahkan (authenticated users) agar mematuhi standard rekabentuk dan pematuhan yang sama seperti yang telah dilaksanakan untuk halaman tetamu (guest pages).

### 1.2. Skop Kemaskini

**Komponen Utama:**

- Dashboard Pengguna Disahkan
- Halaman Profil & Tetapan
- Sejarah Penyerahan (Submission History)
- Pusat Notifikasi (Notification Center)
- Borang Disahkan (Helpdesk & Loan)
- Pautan Akaun (Account Linking)

**Standard Rujukan:**

- `docs/frontend/FRONTEND-DEVELOPMENT-v3-6-0.md` - Panduan pembangunan komprehensif
- `docs/frontend/FRONTPAGE_DESIGN_ANALYSIS_v3.6.0.md` - Analisis rekabentuk halaman utama

### 1.3. Objektif Utama

- ✅ Pematuhan penuh WCAG 2.2 AA (100%)
- ✅ Implementasi MyDS Design System v2025.2 (100%)
- ✅ Konsistensi visual dengan halaman tetamu
- ✅ Sokongan mod terang/gelap penuh
- ✅ Touch targets minimum 44×44px
- ✅ Focus indicators 3px visible
- ✅ Bahasa Melayu sahaja (D15 v3.6.0)

---

## 2. Analisis Keadaan Semasa (Current State Analysis)

### 2.1. Status Pematuhan Komponen

| Komponen | WCAG 2.2 AA | MyDS v2025.2 | Dark Mode | Touch Targets | Focus Ring | Status |
|----------|-------------|--------------|-----------|---------------|------------|--------|
| **Dashboard** | 🟡 85% | 🟡 80% | ✅ 100% | 🟡 90% | 🟡 85% | Perlu Kemaskini |
| **Profile** | 🟡 80% | 🟡 75% | ✅ 100% | 🟡 85% | 🟡 80% | Perlu Kemaskini |
| **Submission History** | 🟡 85% | 🟡 85% | ✅ 100% | ✅ 95% | 🟡 85% | Perlu Kemaskini |
| **Notification Center** | 🟢 90% | 🟡 85% | ✅ 100% | ✅ 95% | ✅ 90% | Penambahbaikan Kecil |
| **Authenticated Forms** | 🟢 95% | 🟢 90% | ✅ 100% | ✅ 100% | ✅ 95% | Hampir Lengkap |
| **Account Linking** | 🟡 80% | 🟡 80% | ✅ 100% | 🟡 85% | 🟡 80% | Perlu Kemaskini |

**Legenda:**

- 🟢 90-100%: Baik
- 🟡 70-89%: Perlu Penambahbaikan
- 🔴 <70%: Kritikal

### 2.2. Isu Kritikal yang Dikenal Pasti

#### P0 (Kritikal - Mesti Diperbaiki Segera)

1. **Dashboard: Touch Targets <44px**
   - Lokasi: Butang tindakan pantas, ikon statistik
   - Impak: Pelanggaran WCAG 2.5.8
   - Cadangan: Tingkatkan ke `min-h-11 min-w-11`

2. **Profile: Form Labels Tidak Lengkap**
   - Lokasi: Borang kemaskini profil
   - Impak: Pelanggaran WCAG 1.3.1, 3.3.2
   - Cadangan: Tambah `aria-describedby`, helper text

3. **Submission History: Table Headers Missing Scope**
   - Lokasi: Jadual sejarah tiket/pinjaman
   - Impak: Pelanggaran WCAG 1.3.1
   - Cadangan: Tambah `scope="col"` pada `<th>`

#### P1 (Tinggi - Patut Diperbaiki)

1. **Dashboard: MyDS Token Tidak Konsisten**
   - Lokasi: Kad statistik, spacing
   - Impak: Ketidakkonsistenan visual
   - Cadangan: Gunakan `shadow-card`, `rounded-l`, `space-y-6`

2. **Notification Center: ARIA Live Region**
   - Lokasi: Notifikasi baharu
   - Impak: Screen reader tidak mengumumkan
   - Cadangan: Tambah `aria-live="polite"`

3. **Account Linking: Focus Management**
   - Lokasi: Modal pautan akaun
   - Impak: Fokus tidak dipindahkan ke modal
   - Cadangan: Implementasi focus trap

---

## 3. Matriks Pematuhan Standard (Standards Compliance Matrix)

### 3.1. WCAG 2.2 AA Checklist

| Kriteria | Dashboard | Profile | History | Notifications | Forms | Linking |
|----------|-----------|---------|---------|---------------|-------|---------|
| **1.1.1** Non-text Content | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **1.3.1** Info & Relationships | 🟡 | 🔴 | 🔴 | ✅ | ✅ | 🟡 |
| **1.4.3** Contrast (Minimum) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **1.4.11** Non-text Contrast | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **2.1.1** Keyboard | ✅ | ✅ | ✅ | ✅ | ✅ | 🟡 |
| **2.4.7** Focus Visible | 🟡 | 🟡 | 🟡 | ✅ | ✅ | 🟡 |
| **2.5.8** Target Size | 🔴 | 🟡 | 🟡 | ✅ | ✅ | 🟡 |
| **3.3.1** Error Identification | ✅ | 🟡 | ✅ | ✅ | ✅ | ✅ |
| **3.3.2** Labels/Instructions | ✅ | 🔴 | ✅ | ✅ | ✅ | 🟡 |
| **4.1.3** Status Messages | 🟡 | ✅ | ✅ | 🟡 | ✅ | ✅ |

### 3.2. MyDS Design System v2025.2 Checklist

| Token Category | Dashboard | Profile | History | Notifications | Forms | Linking |
|----------------|-----------|---------|---------|---------------|-------|---------|
| **Color Tokens** | 🟡 | 🟡 | 🟡 | ✅ | ✅ | 🟡 |
| **Radius Tokens** | 🟡 | 🟡 | ✅ | ✅ | ✅ | 🟡 |
| **Shadow Tokens** | 🔴 | 🔴 | 🟡 | ✅ | ✅ | 🟡 |
| **Spacing Tokens** | 🟡 | 🟡 | ✅ | ✅ | ✅ | ✅ |
| **Typography** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 4. Rancangan Kemaskini Komponen (Component Update Plan)

### 4.1. Dashboard Pengguna Disahkan

**Lokasi Fail:**

- `app/Livewire/Portal/Dashboard.php`
- `resources/views/livewire/portal/dashboard.blade.php`

**Kemaskini Diperlukan:**

#### P0 (Kritikal)

```blade
{{-- SEBELUM: Touch targets <44px --}}
<button class="p-2 rounded hover:bg-gray-100">
    <x-heroicon-o-plus class="w-5 h-5" />
</button>

{{-- SELEPAS: WCAG 2.5.8 compliant --}}
<button class="p-2 rounded hover:bg-gray-100 min-h-11 min-w-11 flex items-center justify-center">
    <x-heroicon-o-plus class="w-5 h-5" />
</button>
```

#### P1 (Tinggi)

```blade
{{-- SEBELUM: Gaya tidak konsisten --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold">Statistik</h3>
</div>

{{-- SELEPAS: MyDS compliant --}}
<div class="bg-white dark:bg-gray-800 rounded-l shadow-card p-6 theme-transition">
    <h3 class="text-lg font-semibold font-heading text-gray-900 dark:text-white">
        Statistik
    </h3>
</div>
```

**Senarai Semak:**

- [ ] Semua butang tindakan minimum 44×44px
- [ ] Kad statistik gunakan `shadow-card`, `rounded-l`
- [ ] Focus ring 3px pada semua elemen interaktif
- [ ] ARIA labels untuk ikon tanpa teks
- [ ] Loading states dengan `wire:loading`

### 4.2. Halaman Profil & Tetapan

**Lokasi Fail:**

- `app/Livewire/Portal/UserProfile.php`
- `resources/views/livewire/portal/user-profile.blade.php`

**Kemaskini Diperlukan:**

#### P0 (Kritikal)

```blade
{{-- SEBELUM: Label tidak lengkap --}}
<input type="text" wire:model="name" class="form-input" />

{{-- SELEPAS: WCAG 3.3.2 compliant --}}
<label for="profile-name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
    {{ __('Nama Penuh') }} <span class="text-danger-600">*</span>
</label>
<input 
    type="text" 
    id="profile-name"
    wire:model.blur="name" 
    class="form-input"
    aria-required="true"
    aria-describedby="name-help name-error"
/>
<p id="name-help" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
    Nama penuh seperti dalam MyKad
</p>
@error('name')
    <p id="name-error" class="mt-1 text-sm text-danger-600" role="alert">
        {{ $message }}
    </p>
@enderror
```

**Senarai Semak:**

- [ ] Semua input ada label dengan `for`/`id`
- [ ] Helper text dengan `aria-describedby`
- [ ] Error messages dengan `role="alert"`
- [ ] Validation real-time dengan `wire:model.blur`
- [ ] Success feedback dengan `aria-live="polite"`

### 4.3. Sejarah Penyerahan (Submission History)

**Lokasi Fail:**

- `app/Livewire/Staff/SubmissionHistory.php`
- `resources/views/livewire/staff/submission-history.blade.php`

**Kemaskini Diperlukan:**

#### P0 (Kritikal)

```blade
{{-- SEBELUM: Table headers tanpa scope --}}
<thead>
    <tr>
        <th>No. Rujukan</th>
        <th>Tarikh</th>
        <th>Status</th>
    </tr>
</thead>

{{-- SELEPAS: WCAG 1.3.1 compliant --}}
<thead>
    <tr>
        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
            No. Rujukan
        </th>
        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
            Tarikh
        </th>
        <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
            Status
        </th>
    </tr>
</thead>
```

**Senarai Semak:**

- [ ] Table headers dengan `scope="col"`
- [ ] Row headers dengan `scope="row"` jika perlu
- [ ] `role="table"` pada container
- [ ] Responsive table dengan horizontal scroll
- [ ] Empty state dengan mesej yang jelas

### 4.4. Pusat Notifikasi (Notification Center)

**Lokasi Fail:**

- `app/Livewire/NotificationCenter.php`
- `resources/views/livewire/notification-center.blade.php`

**Kemaskini Diperlukan:**

#### P1 (Tinggi)

```blade
{{-- SEBELUM: Tiada ARIA live region --}}
<div class="notification-list">
    @foreach($notifications as $notification)
        <div class="notification-item">...</div>
    @endforeach
</div>

{{-- SELEPAS: WCAG 4.1.3 compliant --}}
<div class="notification-list" role="log" aria-live="polite" aria-atomic="false">
    @foreach($notifications as $notification)
        <div class="notification-item" role="listitem">...</div>
    @endforeach
</div>

{{-- Announcement untuk notifikasi baharu --}}
<div class="sr-only" aria-live="assertive" aria-atomic="true">
    @if($newNotificationCount > 0)
        {{ __('Anda mempunyai :count notifikasi baharu', ['count' => $newNotificationCount]) }}
    @endif
</div>
```

**Senarai Semak:**

- [ ] ARIA live region untuk notifikasi baharu
- [ ] Screen reader announcements
- [ ] Mark as read dengan feedback
- [ ] Filter dengan keyboard navigation
- [ ] Loading states untuk fetch

### 4.5. Borang Disahkan (Authenticated Forms)

**Lokasi Fail:**

- `app/Livewire/Helpdesk/AuthenticatedTicketForm.php`
- `app/Livewire/Loan/AuthenticatedLoanForm.php`

**Status:** ✅ Hampir lengkap (95%)

**Kemaskini Kecil:**

- [ ] Pastikan auto-fill data konsisten
- [ ] Validation messages lengkap
- [ ] Success state dengan nombor rujukan
- [ ] Rollback mechanism untuk Optimistic UI

### 4.6. Pautan Akaun (Account Linking)

**Lokasi Fail:**

- `app/Livewire/Portal/AccountLinking.php`
- `resources/views/livewire/portal/account-linking.blade.php`

**Kemaskini Diperlukan:**

#### P0 (Kritikal)

```javascript
// SEBELUM: Tiada focus management
Livewire.on('modalOpened', () => {
    // Tiada tindakan
});

// SELEPAS: Focus trap implementation
Livewire.on('modalOpened', () => {
    setTimeout(() => {
        const modal = document.querySelector('[role="dialog"]');
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) firstFocusable.focus();
    }, 100);
});
```

**Senarai Semak:**

- [ ] Modal dengan `role="dialog"`, `aria-modal="true"`
- [ ] Focus trap dengan Alpine.js `x-trap`
- [ ] ESC key untuk tutup modal
- [ ] Backdrop click untuk tutup
- [ ] Success feedback selepas pautan

---

## 5. Fasa Pelaksanaan (Implementation Phases)

### Fasa 1: Pembetulan Kritikal (P0) - 1 Minggu

**Minggu 1:**

- [ ] Dashboard: Touch targets 44×44px
- [ ] Profile: Form labels lengkap
- [ ] Submission History: Table scope attributes
- [ ] Account Linking: Focus management

**Output:** Pematuhan WCAG 2.2 AA minimum 90%

### Fasa 2: Penambahbaikan MyDS (P1) - 1 Minggu

**Minggu 2:**

- [ ] Dashboard: MyDS tokens konsisten
- [ ] Profile: Shadow dan radius tokens
- [ ] Notification Center: ARIA live regions
- [ ] Semua komponen: Focus ring 3px

**Output:** Pematuhan MyDS Design System v2025.2 minimum 95%

### Fasa 3: Pengujian & Pengesahan (P2) - 3 Hari

**Hari 1-2:**

- [ ] Automated testing (Playwright + Axe)
- [ ] Manual testing (NVDA/JAWS)
- [ ] Cross-browser testing
- [ ] Dark mode verification

**Hari 3:**

- [ ] Documentation updates
- [ ] MCP Memory Server updates
- [ ] Final validation

**Output:** Pematuhan penuh 100%

---

## 6. Strategi Pengujian (Testing Strategy)

### 6.1. Automated Testing

```typescript
// tests/e2e/authenticated-accessibility.spec.ts
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Authenticated Pages Accessibility', () => {
    test.beforeEach(async ({ page }) => {
        // Login sebagai pengguna disahkan
        await page.goto('/login');
        await page.fill('[name="email"]', 'test@motac.gov.my');
        await page.fill('[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForURL('/dashboard');
    });

    test('Dashboard should not have WCAG violations', async ({ page }) => {
        const accessibilityScanResults = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
            .analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
    });

    test('Profile page should not have WCAG violations', async ({ page }) => {
        await page.goto('/profile');
        
        const accessibilityScanResults = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
            .analyze();

        expect(accessibilityScanResults.violations).toEqual([]);
    });
});
```

### 6.2. Manual Testing Checklist

| Ujian | Dashboard | Profile | History | Notifications | Forms | Linking |
|-------|-----------|---------|---------|---------------|-------|---------|
| **Keyboard Navigation** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| **Screen Reader (NVDA)** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| **Focus Indicators** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| **Touch Targets (Mobile)** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| **Dark Mode** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |
| **Zoom 200%** | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ | ⬜ |

---

## 7. Metrik Kejayaan (Success Metrics)

### 7.1. Sasaran Pematuhan

| Metrik | Sasaran | Kaedah Pengukuran |
|--------|---------|-------------------|
| **WCAG 2.2 AA Score** | 100% | Lighthouse + Axe DevTools |
| **MyDS Token Usage** | 100% | Code review |
| **Touch Targets ≥44px** | 100% | Manual measurement |
| **Focus Indicators** | 100% | Visual inspection |
| **Dark Mode Support** | 100% | Visual testing |
| **Screen Reader Compat** | 100% | NVDA/JAWS testing |

### 7.2. Performance Targets

| Metrik | Sasaran |
|--------|---------|
| **Lighthouse Performance** | ≥90 |
| **Lighthouse Accessibility** | ≥95 |
| **First Contentful Paint** | <1.5s |
| **Largest Contentful Paint** | <2.5s |
| **Cumulative Layout Shift** | <0.1 |

---

## 8. Rujukan dan Sumber (References and Resources)

### 8.1. Dokumentasi Dalaman

- **FRONTEND-DEVELOPMENT-v3-6-0.md** - Panduan pembangunan komprehensif
- **FRONTPAGE_DESIGN_ANALYSIS_v3.6.0.md** - Analisis rekabentuk halaman utama
- **D00-D17** - Dokumentasi sistem lengkap

### 8.2. Standard Luaran

- **WCAG 2.2 AA**: <https://www.w3.org/TR/WCAG22/>
- **MyDS Design System v2025.2**: <https://design.digital.gov.my/>
- **Livewire 3.7 Docs**: <https://livewire.laravel.com/docs/3.x>
- **Tailwind CSS v4**: <https://tailwindcss.com/docs>

---

## 9. Kesimpulan dan Langkah Seterusnya

### 9.1. Ringkasan

Rancangan ini menyediakan roadmap yang jelas untuk mengemaskini semua komponen frontend pengguna disahkan agar mematuhi standard yang sama seperti halaman tetamu. Fokus utama adalah pada pematuhan WCAG 2.2 AA, implementasi MyDS Design System v2025.2, dan konsistensi visual merentas keseluruhan sistem.

### 9.2. Tindakan Segera

1. **Minggu 1:** Pembetulan kritikal (P0)
2. **Minggu 2:** Penambahbaikan MyDS (P1)
3. **Minggu 3:** Pengujian dan pengesahan

### 9.3. Pemantauan Berterusan

- **Audit Bulanan:** Lighthouse + Axe DevTools
- **Ujian Pengguna:** UAT setiap suku tahun
- **Performance Monitoring:** Laravel Pulse dashboard
- **Compliance Review:** Semakan D00-D17 setiap 6 bulan

---

**Status Dokumen:** ✅ Aktif - Perancangan  
**Tarikh Dicipta:** 13 Disember 2025  
**Kemaskini Terakhir:** 13 Disember 2025  
**Semakan Seterusnya:** 13 Mac 2026  
**Diselenggara Oleh:** Pasukan Pembangunan Frontend  
**Diluluskan Oleh:** Jawatankuasa Seni Bina Teknikal  
**Pematuhan:** D00-D17 v3.6.0, MyDS Design System v2025.2, WCAG 2.2 AA
