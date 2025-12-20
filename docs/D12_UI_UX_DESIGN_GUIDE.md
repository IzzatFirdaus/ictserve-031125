# D12: Panduan Reka Bentuk UI/UX (UI/UX Design Guide)

## ICTServe v3.6.1 - True Hybrid Architecture dengan AI Integration

| Atribut | Nilai |
|---------|-------|
| **Versi** | 3.6.1 |
| **Tarikh Kemaskini** | 14 Disember 2025 |
| **Status** | Aktif - Sedia untuk Pelaksanaan |
| **Klasifikasi** | Terhad - Dalaman BPM MOTAC |
| **Pematuhi** | WCAG 2.2 AA, MyGOV Digital Service Standards v2.1.0, ISO/IEC/IEEE 29148, ISO/IEC/IEEE 42010 |
| **Bahasa** | Bahasa Melayu sahaja (D15 v3.6.0) |

> **Notis Penggunaan Dalaman**: Sistem ini adalah untuk kegunaan warga kerja MOTAC (staf dan pegawai gred) sahaja dan tidak dibuka kepada orang awam (internal use only).

---

## Sejarah Perubahan (Changelog)

| Versi | Tarikh | Perubahan | Penulis |
|-------|--------|-----------|---------|
| 3.6.1 | 2025-12-17 | Kemaskini teknologi stack: Laravel 12.43.1, Livewire 3.7.3, Laravel Pulse 1.4.7, Laravel Reverb 1.6.3, Laravel Sanctum 4.2.1, Laravel Socialite 5.24.0, PHPUnit 11.5.46, Tailwind CSS 4.1.18, Laravel MCP 0.3.4, Laravel Prompts 0.3.8, Larastan 3.8.1, Laravel Pint 1.26.0, Laravel Telescope 5.16.0, Laravel Horizon 5.41.0. Kemaskini versi sistem kepada ICTServe v3.6.1, penyelarasan dengan D00-D18 v3.6.1, pengesahan Bahasa Melayu sahaja (v3.6.0+). | Pasukan Pembangunan BPM |
| 3.6.0 | 2025-12-14 | Integrasi lengkap D18 Cloud Hybrid AI Architecture - AI chat interface, streaming responses, model selection, conversation management, web-augmented responses, WCAG 2.2 AA compliance | Pasukan Pembangunan BPM |
| 3.5.0 | 2025-11-01 | Kemaskini untuk Laravel Reverb, Laravel Pulse, dan pematuhan WCAG 2.2 AA | Pasukan Pembangunan BPM |
| 3.4.0 | 2025-10-15 | Penyepaduan Filament v4, Livewire v3, dan Tailwind v4 | Pasukan Pembangunan BPM |
| 3.3.0 | 2025-09-01 | Kemaskini untuk True Hybrid Architecture dan Self-Registration | Pasukan Pembangunan BPM |

---

## Rujukan Dokumen Berkaitan (Related Document References)

| Dokumen | Penerangan | Versi |
|---------|------------|-------|
| [D00_SYSTEM_OVERVIEW.md](D00_SYSTEM_OVERVIEW.md) | Gambaran keseluruhan sistem dan tadbir urus | v3.6.1 |
| [D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md](D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md) | Spesifikasi keperluan perisian | v3.6.1 |
| [D04_SOFTWARE_DESIGN_DOCUMENT.md](D04_SOFTWARE_DESIGN_DOCUMENT.md) | Seni bina dan reka bentuk | v3.6.1 |
| [D13_UI_UX_FRONTEND_FRAMEWORK.md](D13_UI_UX_FRONTEND_FRAMEWORK.md) | Framework frontend (Livewire/Volt) | v3.6.1 |
| [D14_UI_UX_STYLE_GUIDE.md](D14_UI_UX_STYLE_GUIDE.md) | Panduan gaya (MyDS v2025.2) | v3.6.1 |
| [D15_LANGUAGE_MS_EN.md](D15_LANGUAGE_MS_EN.md) | Penyetempatan bahasa (Bahasa Melayu sahaja) | v3.7.0 |
| [D18_AI_CHATBOT_OLLAMA_BEDROCK.md](D18_AI_CHATBOT_OLLAMA_BEDROCK.md) | Cloud Hybrid AI Architecture | v1.0.1 |

---

## Kandungan (Table of Contents)

1. [Glosari (Glossary)](#1-glosari-glossary)
2. [Ringkasan Eksekutif (Executive Summary)](#2-ringkasan-eksekutif-executive-summary)
3. [Prinsip Reka Bentuk (Design Principles)](#3-prinsip-reka-bentuk-design-principles)
4. [Sistem Warna (Color System)](#4-sistem-warna-color-system)
5. [Tipografi (Typography)](#5-tipografi-typography)
6. [Struktur Halaman (Page Structure)](#6-struktur-halaman-page-structure)
7. [Antara Muka AI (AI Interface Design)](#7-antara-muka-ai-ai-interface-design)
8. [Integrasi dengan D18 Cloud Hybrid AI Architecture](#8-integrasi-dengan-d18-cloud-hybrid-ai-architecture)
9. [Rujukan Silang dengan D00-D17](#9-rujukan-silang-dengan-d00-d17)
10. [Kesimpulan](#10-kesimpulan)

---

## 1. Glosari (Glossary)

| Istilah | Definisi |
|---------|----------|
| **WCAG 2.2 AA** | Web Content Accessibility Guidelines Level AA - standard kebolehcapaian web |
| **MyGOV Digital Service Standards** | Standard perkhidmatan digital kerajaan Malaysia v2.1.0 |
| **True Hybrid Architecture** | Seni bina hibrid sebenar ICTServe dengan self-registration dan akses fleksibel |
| **AI Chat Interface** | Antara muka perbualan AI yang terintegrasi dengan D18 Cloud Hybrid AI Architecture |
| **Streaming Responses** | Respons AI yang dihantar secara berperingkat untuk pengalaman pengguna yang responsif |
| **Model Selection** | Pemilihan model AI (Auto, Claude Opus, Sonnet, Haiku) berdasarkan jenis tugas |
| **Web-Augmented Responses** | Respons AI yang diperkaya dengan maklumat terkini dari carian web |
| **Conversation Management** | Pengurusan konteks perbualan AI dengan memori jangka panjang |
| **Core Web Vitals** | Metrik prestasi web utama (LCP, FID, CLS) |
| **Progressive Enhancement** | Pendekatan pembangunan web yang bermula dengan fungsi asas |

---

## 2. Ringkasan Eksekutif (Executive Summary)

### 2.1 Tujuan (Purpose)

Dokumen D12 ini menyediakan panduan komprehensif untuk reka bentuk antara muka pengguna (UI) dan pengalaman pengguna (UX) bagi sistem ICTServe v3.6.0. Panduan ini merangkumi:

- **Integrasi AI Interface**: Reka bentuk antara muka AI chat yang mematuhi D18 Cloud Hybrid AI Architecture
- **WCAG 2.2 AA Compliance**: Kebolehcapaian penuh untuk semua pengguna termasuk OKU
- **True Hybrid Architecture Support**: UI yang menyokong akses tetamu dan authenticated seamlessly
- **MyGOV Standards Alignment**: Pematuhan dengan standard perkhidmatan digital kerajaan Malaysia

### 2.2 Skop (Scope)

Panduan ini meliputi:

1. **Sistem Reka Bentuk**: Warna, tipografi, spacing, dan komponen
2. **Antara Muka AI**: Chat interface, streaming responses, model selection
3. **Kebolehcapaian**: WCAG 2.2 AA compliance dan sokongan teknologi bantuan
4. **Prestasi**: Core Web Vitals dan pengoptimuman
5. **Responsif**: Mobile-first design dan adaptive layouts

---

## 3. Prinsip Reka Bentuk (Design Principles)

### 3.1 Kebolehcapaian Dahulu (Accessibility First)

- **WCAG 2.2 AA Compliance**: Semua komponen mematuhi standard kebolehcapaian
- **Kontras Warna**: Minimum 4.5:1 untuk teks, 3:1 untuk elemen UI
- **Navigasi Papan Kekunci**: Sokongan penuh untuk pengguna papan kekunci
- **Teknologi Bantuan**: Kompatibel dengan pembaca skrin dan alat bantuan lain

### 3.2 Mobile-First Design

- **Responsive Layout**: Reka bentuk yang menyesuaikan dengan semua saiz skrin
- **Touch-Friendly**: Saiz sasaran sentuh minimum 44×44px
- **Progressive Enhancement**: Fungsi asas berfungsi tanpa JavaScript

### 3.3 Prestasi Optimum

- **Core Web Vitals**: LCP < 2.5s, FID < 100ms, CLS < 0.1
- **Lazy Loading**: Pemuatan komponen secara berperingkat
- **Caching Strategy**: Strategi caching yang cekap

### 3.4 Konsistensi

- **Design System**: Komponen yang konsisten di seluruh aplikasi
- **Bahasa Melayu**: Antara muka dalam Bahasa Melayu sahaja (D15 v3.6.0)
- **Branding**: Pematuhan dengan identiti visual kerajaan

---

## 4. Sistem Warna (Color System)

### 4.1 Palet Warna Utama

#### Primary Colors (Warna Utama)

| Warna | Hex | RGB | Kontras | Penggunaan |
|-------|-----|-----|---------|------------|
| Primary 50 | #eff6ff | rgb(239, 246, 255) | - | Background light |
| Primary 500 | #0056B3 | rgb(0, 86, 179) | 7.2:1 | Main actions, links |
| Primary 600 | #004494 | rgb(0, 68, 148) | 8.1:1 | Hover states |
| Primary 700 | #003875 | rgb(0, 56, 117) | 9.8:1 | Active states |

#### Secondary Colors (Warna Sekunder)

| Warna | Hex | RGB | Kontras | Penggunaan |
|-------|-----|-----|---------|------------|
| Secondary 500 | #0B4D8F | rgb(11, 77, 143) | 8.1:1 | Secondary actions |
| Secondary 600 | #094070 | rgb(9, 64, 112) | 9.5:1 | Secondary hover |

#### Semantic Colors (Warna Semantik)

| Warna | Hex | RGB | Kontras | Penggunaan |
|-------|-----|-----|---------|------------|
| Success 500 | #1B7C54 | rgb(27, 124, 84) | 4.6:1 | Success states |
| Warning 500 | #CC7700 | rgb(204, 119, 0) | 4.5:1 | Warning states |
| Danger 500 | #B3002D | rgb(179, 0, 45) | 7.8:1 | Error states |
| Info 500 | #0369A1 | rgb(3, 105, 161) | 6.2:1 | Information |

### 4.2 Neutral Colors (Warna Neutral)

| Warna | Hex | RGB | Kontras | Penggunaan |
|-------|-----|-----|---------|------------|
| Gray 50 | #f9fafb | rgb(249, 250, 251) | - | Background |
| Gray 100 | #f3f4f6 | rgb(243, 244, 246) | - | Light background |
| Gray 200 | #e5e7eb | rgb(229, 231, 235) | 3:1 | Borders |
| Gray 300 | #d1d5db | rgb(209, 213, 219) | 3.8:1 | Disabled states |
| Gray 500 | #6b7280 | rgb(107, 114, 128) | 4.6:1 | Secondary text |
| Gray 700 | #374151 | rgb(55, 65, 81) | 8.9:1 | Body text |
| Gray 900 | #111827 | rgb(17, 24, 39) | 12.6:1 | Headings |

### 4.3 AI Interface Colors

| Warna | Hex | RGB | Kontras | Penggunaan |
|-------|-----|-----|---------|------------|
| AI User Message | #0056B3 | rgb(0, 86, 179) | 7.2:1 | User message background |
| AI Assistant Message | #ffffff | rgb(255, 255, 255) | - | AI response background |
| AI Streaming Indicator | #f3f4f6 | rgb(243, 244, 246) | - | Streaming response |
| Model Auto | #0056B3 | rgb(0, 86, 179) | 7.2:1 | Auto routing |
| Model Opus | #7c3aed | rgb(124, 58, 237) | 5.8:1 | Claude Opus |
| Model Sonnet | #2563eb | rgb(37, 99, 235) | 6.1:1 | Claude Sonnet |
| Model Haiku | #059669 | rgb(5, 150, 105) | 4.8:1 | Claude Haiku |

---

## 5. Tipografi (Typography)

### 5.1 Font Families

#### Primary Font: Poppins (Headings)

```css
font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

#### Secondary Font: Inter (Body Text)

```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

#### Monospace Font: JetBrains Mono (Code)

```css
font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
```

### 5.2 Type Scale

#### Headings

| Level | Size | Line Height | Font Weight | Tailwind Class |
|-------|------|-------------|-------------|----------------|
| H1 | 36px (2.25rem) | 44px (2.75rem) | 600 (Semibold) | `text-4xl font-semibold` |
| H2 | 30px (1.875rem) | 38px (2.375rem) | 600 (Semibold) | `text-3xl font-semibold` |
| H3 | 24px (1.5rem) | 32px (2rem) | 600 (Semibold) | `text-2xl font-semibold` |
| H4 | 20px (1.25rem) | 28px (1.75rem) | 600 (Semibold) | `text-xl font-semibold` |
| H5 | 16px (1rem) | 24px (1.5rem) | 600 (Semibold) | `text-base font-semibold` |
| H6 | 14px (0.875rem) | 20px (1.25rem) | 600 (Semibold) | `text-sm font-semibold` |

#### Body Text

| Type | Size | Line Height | Font Weight | Tailwind Class |
|------|------|-------------|-------------|----------------|
| Large | 18px (1.125rem) | 28px (1.75rem) | 400 (Regular) | `text-lg` |
| Base | 16px (1rem) | 24px (1.5rem) | 400 (Regular) | `text-base` |
| Small | 14px (0.875rem) | 20px (1.25rem) | 400 (Regular) | `text-sm` |
| Extra Small | 12px (0.75rem) | 16px (1rem) | 400 (Regular) | `text-xs` |

### 5.3 Font Weights

| Weight | Value | Tailwind Class | Usage |
|--------|-------|----------------|-------|
| Regular | 400 | `font-normal` | Body text |
| Medium | 500 | `font-medium` | Labels, captions |
| Semibold | 600 | `font-semibold` | Headings, buttons |
| Bold | 700 | `font-bold` | Emphasis |

---

## 6. Struktur Halaman (Page Structure)

### 6.1 Layout Hierarchy

#### Guest Layout (`resources/views/layouts/guest.blade.php`)

```text
┌─────────────────────────────────────┐
│ Header (Navigation + Language)      │
├─────────────────────────────────────┤
│                                     │
│ Main Content Area                   │
│ - Hero Section                      │
│ - Form/Content                      │
│ - AI Chat Widget (if enabled)       │
│                                     │
├─────────────────────────────────────┤
│ Footer (Links + Copyright)          │
└─────────────────────────────────────┘
```

#### Authenticated Layout (`resources/views/layouts/app.blade.php`)

```text
┌─────────────────────────────────────┐
│ Header (Nav + Notifications + User) │
├─────────────────────────────────────┤
│ Sidebar │ Main Content Area         │
│ - Menu  │ - Breadcrumbs            │
│ - Stats │ - Page Content           │
│         │ - AI Assistant           │
│         │                          │
├─────────────────────────────────────┤
│ Footer (System Info + Links)        │
└─────────────────────────────────────┘
```

#### Admin Layout (Filament)

```text
┌─────────────────────────────────────┐
│ Admin Header (Filament Navigation)  │
├─────────────────────────────────────┤
│ Sidebar │ Admin Content            │
│ - Admin │ - Dashboard/Resources    │
│   Menu  │ - AI Analytics           │
│ - Stats │ - System Monitoring      │
│         │                          │
└─────────────────────────────────────┘
```

### 6.2 Grid System

#### Container Sizes

| Breakpoint | Container Width | Padding |
|------------|----------------|---------|
| Mobile (< 640px) | 100% | 16px |
| Tablet (640px+) | 640px | 24px |
| Desktop (768px+) | 768px | 32px |
| Large (1024px+) | 1024px | 40px |
| XL (1280px+) | 1280px | 48px |

#### Grid Columns

```css
/* 12-column grid system */
.grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)); }

/* Common layouts */
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }  /* Mobile */
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }  /* Tablet */
.grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }  /* Desktop */
.grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }  /* Large */
```

---

## 7. Antara Muka AI (AI Interface Design)

### 7.1 Chat Interface Components

#### 7.1.1. Main Chat Container

**Layout Structure**:

```blade
<div class="ai-chat-container flex flex-col h-full max-h-screen bg-white rounded-lg shadow-card">
    {{-- Chat Header --}}
    <div class="chat-header flex items-center justify-between p-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                <x-heroicon-s-chat-bubble-left-right class="w-5 h-5 text-white" />
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ __('AI Assistant') }}</h3>
                <p class="text-xs text-gray-500">{{ __('Powered by Claude & Ollama') }}</p>
            </div>
        </div>
        
        {{-- Model Selection --}}
        <div class="model-selector">
            @include('components.ai.model-selector')
        </div>
    </div>

    {{-- Chat Messages --}}
    <div class="chat-messages flex-1 overflow-y-auto p-4 space-y-4" 
         role="log" 
         aria-live="polite" 
         aria-label="{{ __('Sejarah perbualan AI') }}">
        @foreach($messages as $message)
            @include('components.ai.message', ['message' => $message])
        @endforeach
        
        {{-- Streaming indicator --}}
        @if($isStreaming)
            <div class="streaming-message bg-gray-50 border-l-4 border-primary-500 p-3 rounded-r-lg">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">{{ __('AI sedang menaip...') }}</span>
                </div>
                <div class="streaming-content prose prose-sm max-w-none"></div>
            </div>
        @endif
    </div>

    {{-- Chat Input --}}
    <div class="chat-input border-t border-gray-200 p-4">
        @include('components.ai.chat-input')
    </div>

    {{-- FAQ Suggestions (if available) --}}
    @if(!empty($faqSuggestions))
        <div class="faq-suggestions border-t border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Cadangan FAQ:') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach($faqSuggestions as $suggestion)
                    <button wire:click="selectFaqSuggestion('{{ $suggestion }}')"
                            class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                        {{ $suggestion }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
```

#### 7.1.2. Streaming Response UI Patterns

**WebSocket (Laravel Reverb + Echo) untuk status AI (admin/superuser)**:

```javascript
// resources/js/bootstrap.js
// Ringkasan: subscribe ke channel AI untuk notifikasi status dan amaran prestasi.
window.initAIBroadcasting = function (userRole) {
    if (!window.Echo) return;
    if (!['admin', 'superuser'].includes(userRole)) return;

    window.Echo.private('ai-status')
        .listen('.AIProcessingStarted', (data) => {
            window.dispatchEvent(new CustomEvent('ai:processing:started', { detail: data }));
        })
        .listen('.AIProcessingCompleted', (data) => {
            window.dispatchEvent(new CustomEvent('ai:processing:completed', { detail: data }));
        });
};
```

#### 7.1.3. Model Selection Interface

**Visual Model Indicators**:

| Model | Icon | Color | Usage Indicator |
|-------|------|-------|-----------------|
| Auto (Pintar) | 🤖 | `text-primary-600` | Routing automatik berdasarkan jenis pertanyaan |
| Claude Opus | 💎 | `text-purple-600` | Tugas kompleks, analisis mendalam |
| Claude Sonnet | ⚡ | `text-blue-600` | Keseimbangan prestasi dan kualiti |
| Claude Haiku | 🚀 | `text-green-600` | Respons pantas, soalan mudah |

**Model Selection Component**:

```blade
<div class="model-selector flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
    <span class="text-sm font-medium text-gray-700">{{ __('Model AI:') }}</span>
    <div class="flex gap-1">
        @foreach(['auto', 'opus', 'sonnet', 'haiku'] as $model)
            <button wire:click="selectModel('{{ $model }}')"
                    class="px-3 py-1 text-xs rounded-md transition-colors
                           {{ $selectedModel === $model 
                              ? 'bg-primary-600 text-white' 
                              : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                {{ $this->getModelLabel($model) }}
            </button>
        @endforeach
    </div>
</div>
```

#### 7.1.4. Conversation Management UI

**Conversation History Sidebar**:

```blade
<div class="conversation-sidebar w-64 bg-white shadow-card rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">{{ __('Sejarah Perbualan') }}</h3>
        <button wire:click="startNewConversation" 
                class="text-sm text-primary-600 hover:text-primary-700">
            {{ __('Baru') }}
        </button>
    </div>
    
    <div class="space-y-2 max-h-64 overflow-y-auto">
        @foreach($savedConversations as $conversation)
            <div class="conversation-item p-2 rounded-md cursor-pointer hover:bg-gray-50
                        {{ $currentConversationId === $conversation->id ? 'bg-primary-50 border-l-2 border-primary-500' : '' }}"
                 wire:click="loadConversation({{ $conversation->id }})">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $conversation->title ?? __('Perbualan Tanpa Tajuk') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $conversation->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    <button wire:click.stop="deleteConversation({{ $conversation->id }})"
                            class="text-gray-400 hover:text-red-500">
                        <x-heroicon-o-trash class="w-4 h-4" />
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
```

### 7.2. Web-Augmented Response Display

**Enhanced Response with Sources**:

```blade
<div class="ai-response bg-white shadow-sm rounded-lg p-4">
    {{-- Response Header --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-success-500 rounded-full"></div>
            <span class="text-sm font-medium text-gray-700">
                {{ __('Respons AI') }}
                @if($response['web_augmented'])
                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        {{ __('Diperkaya Web') }}
                    </span>
                @endif
            </span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">{{ $response['model_used'] }}</span>
            <span class="text-xs text-gray-400">{{ $response['response_time'] }}ms</span>
        </div>
    </div>

    {{-- Main Response Content --}}
    <div class="prose prose-sm max-w-none">
        {!! Str::markdown($response['content']) !!}
    </div>

    {{-- Web Sources (if applicable) --}}
    @if(!empty($response['web_sources']))
        <div class="mt-4 pt-3 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Sumber Web:') }}</p>
            <div class="space-y-2">
                @foreach($response['web_sources'] as $source)
                    <div class="flex items-center gap-2 text-sm">
                        <x-heroicon-o-link class="w-4 h-4 text-gray-400" />
                        <a href="{{ $source['url'] }}" target="_blank" 
                           class="text-primary-600 hover:text-primary-700 truncate">
                            {{ $source['title'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- FAQ Sources (if applicable) --}}
    @if(!empty($response['faq_sources']))
        <div class="mt-4 pt-3 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Sumber FAQ:') }}</p>
            <div class="space-y-1">
                @foreach($response['faq_sources'] as $faq)
                    <div class="text-sm text-gray-600">
                        • {{ $faq['question'] }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
```

### 7.3. AI Interface Accessibility (WCAG 2.2 AA)

#### 7.3.1. Keyboard Navigation

**Chat Interface Keyboard Support**:

| Key Combination | Action | Implementation |
|----------------|--------|----------------|
| `Tab` | Navigate between chat input, model selector, suggestions | Standard focus management |
| `Ctrl + Enter` | Send message from textarea | `@keydown.ctrl.enter` directive |
| `Escape` | Clear current input or close suggestions | Alpine.js event handler |
| `Arrow Up/Down` | Navigate through message history | Custom keyboard handler |
| `Alt + M` | Open model selection dropdown | Keyboard shortcut |

**Implementation**:

```blade
<div class="ai-chat-interface" 
     x-data="aiChatKeyboard()" 
     @keydown.window="handleKeydown($event)">
    
    {{-- Chat input with keyboard shortcuts --}}
    <textarea wire:model="userMessage"
              @keydown.ctrl.enter="$wire.sendMessage()"
              @keydown.escape="clearInput()"
              @keydown.arrow-up="navigateHistory('up')"
              @keydown.arrow-down="navigateHistory('down')"
              aria-label="{{ __('Masukkan pertanyaan anda') }}"
              aria-describedby="chat-help-text">
    </textarea>
    
    <div id="chat-help-text" class="sr-only">
        {{ __('Gunakan Ctrl+Enter untuk menghantar, Escape untuk membersihkan, Arrow keys untuk sejarah') }}
    </div>
</div>
```

#### 7.3.2. Screen Reader Support

**ARIA Labels and Live Regions**:

```blade
{{-- Chat messages with proper ARIA --}}
<div class="chat-messages" 
     role="log" 
     aria-live="polite" 
     aria-label="{{ __('Sejarah perbualan AI') }}">
    
    @foreach($conversations as $message)
        <div class="message" 
             role="article"
             aria-label="{{ $message['role'] === 'user' ? __('Mesej anda') : __('Respons AI') }}">
            
            {{-- Message content with proper markup --}}
            <div class="message-content">
                @if($message['role'] === 'assistant')
                    <div class="ai-indicator" aria-label="{{ __('Respons dari AI Assistant') }}">
                        <span class="sr-only">{{ __('AI berkata:') }}</span>
                    </div>
                @endif
                
                <div class="prose" aria-label="{{ __('Kandungan mesej') }}">
                    {!! Str::markdown($message['content']) !!}
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Model selection with proper labeling --}}
<fieldset class="model-selection">
    <legend class="sr-only">{{ __('Pilih model AI') }}</legend>
    <div class="flex gap-2" role="radiogroup" aria-label="{{ __('Model AI yang tersedia') }}">
        @foreach($models as $model)
            <label class="model-option">
                <input type="radio" 
                       name="ai_model" 
                       value="{{ $model }}"
                       wire:model.live="selectedModel"
                       aria-describedby="model-{{ $model }}-desc">
                <span>{{ $this->getModelLabel($model) }}</span>
                <div id="model-{{ $model }}-desc" class="sr-only">
                    {{ $this->getModelDescription($model) }}
                </div>
            </label>
        @endforeach
    </div>
</fieldset>
```

#### 7.3.3. Color Contrast and Visual Indicators

**High Contrast AI Interface Elements**:

```css
/* AI Chat Interface - WCAG 2.2 AA Compliant */
.ai-chat-container {
    /* Ensure 4.5:1 contrast ratio for text */
    --ai-text-primary: #1f2937; /* 12.6:1 contrast on white */
    --ai-text-secondary: #6b7280; /* 4.6:1 contrast on white */
    --ai-bg-user: #0056b3; /* 7.2:1 contrast with white text */
    --ai-bg-assistant: #ffffff; /* White background */
    --ai-border-focus: #0b4d8f; /* 8.1:1 contrast for focus rings */
}

.user-message {
    background-color: var(--ai-bg-user);
    color: white; /* 7.2:1 contrast ratio */
}

.ai-message {
    background-color: var(--ai-bg-assistant);
    color: var(--ai-text-primary);
    border: 1px solid #e5e7eb; /* 3:1 contrast for UI elements */
}

/* Streaming indicator with sufficient contrast */
.streaming-indicator {
    background-color: #f3f4f6;
    border-left: 3px solid #0056b3; /* Visual indicator */
}

/* Model selection with clear visual states */
.model-selector button {
    transition: all 0.2s ease;
}

.model-selector button:focus {
    outline: 3px solid var(--ai-border-focus);
    outline-offset: 2px;
}

.model-selector button[aria-pressed="true"] {
    background-color: var(--ai-bg-user);
    color: white;
}
```

### 7.4. Performance Optimization for AI Interface

#### 7.4.1. Lazy Loading and Progressive Enhancement

**Conversation History Virtualization**:

```blade
{{-- Virtual scrolling for large conversation histories --}}
<div class="conversation-history" 
     x-data="virtualScroll({ itemHeight: 60, totalItems: {{ $totalMessages }} })"
     x-init="initVirtualScroll()"
     @scroll="handleScroll($event)">
    
    {{-- Render only visible messages --}}
    <template x-for="(message, index) in visibleMessages" :key="message.id">
        <div class="message-item" 
             :style="`transform: translateY(${index * itemHeight}px)`">
            <!-- Message content -->
        </div>
    </template>
</div>
```

#### 7.4.2. Streaming Response Optimization

**Efficient DOM Updates**:

```javascript
// Optimized streaming response handler
class StreamingResponseHandler {
    constructor(container) {
        this.container = container;
        this.buffer = '';
        this.updateThrottle = 16; // 60fps
        this.lastUpdate = 0;
    }

    appendContent(chunk) {
        this.buffer += chunk;
        
        const now = performance.now();
        if (now - this.lastUpdate > this.updateThrottle) {
            this.flushBuffer();
            this.lastUpdate = now;
        }
    }

    flushBuffer() {
        if (this.buffer) {
            const messageElement = this.container.querySelector('.streaming-message');
            messageElement.innerHTML = this.renderMarkdown(this.buffer);
            this.buffer = '';
            this.scrollToBottom();
        }
    }
}
```

### 7.5. AI Interface Testing Guidelines

#### 7.5.1. Automated Accessibility Testing

**Playwright E2E Tests for AI Interface**:

```typescript
// tests/e2e/ollama-accessibility.spec.ts
import { test, expect } from '@playwright/test';

test.describe('AI Chat Interface Accessibility', () => {
    test('should have proper ARIA labels and roles', async ({ page }) => {
        await page.goto('/bedrock-chat');
        
        // Check chat container has proper role
        const chatContainer = page.locator('[role="log"]');
        await expect(chatContainer).toBeVisible();
        
        // Check message input has proper labeling
        const messageInput = page.locator('textarea[aria-label*="pertanyaan"]');
        await expect(messageInput).toBeVisible();
        
        // Check model selection has proper fieldset
        const modelFieldset = page.locator('fieldset legend:has-text("model")');
        await expect(modelFieldset).toBeVisible();
    });

    test('should support keyboard navigation', async ({ page }) => {
        await page.goto('/bedrock-chat');
        
        // Tab through interface elements
        await page.keyboard.press('Tab');
        await expect(page.locator('textarea')).toBeFocused();
        
        await page.keyboard.press('Tab');
        await expect(page.locator('.model-selector button:first-child')).toBeFocused();
        
        // Test Ctrl+Enter shortcut
        await page.locator('textarea').fill('Test message');
        await page.keyboard.press('Control+Enter');
        
        // Verify message was sent
        await expect(page.locator('.user-message')).toContainText('Test message');
    });

    test('should meet color contrast requirements', async ({ page }) => {
        await page.goto('/ai-chat');
        
        // Check contrast ratios using axe-core
        const results = await page.evaluate(() => {
            return new Promise((resolve) => {
                axe.run(document, {
                    tags: ['wcag2a', 'wcag2aa', 'wcag21aa']
                }, (err, results) => {
                    resolve(results);
                });
            });
        });
        
        expect(results.violations).toHaveLength(0);
    });
});
```

#### 7.5.2. Performance Testing

**Core Web Vitals for AI Interface**:

```javascript
// Performance monitoring for AI chat
class AIChatPerformanceMonitor {
    constructor() {
        this.metrics = {
            firstContentfulPaint: 0,
            largestContentfulPaint: 0,
            firstInputDelay: 0,
            cumulativeLayoutShift: 0,
            streamingLatency: 0
        };
    }

    measureStreamingLatency() {
        const startTime = performance.now();
        
        return {
            markStart: () => {
                this.streamingStartTime = performance.now();
            },
            markFirstChunk: () => {
                this.metrics.streamingLatency = performance.now() - this.streamingStartTime;
                console.log(`Streaming first chunk: ${this.metrics.streamingLatency}ms`);
            }
        };
    }

    reportMetrics() {
        // Send to Laravel Pulse for monitoring
        fetch('/pulse/ai-metrics', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(this.metrics)
        });
    }
}
```

---

## 8. Integrasi dengan D18 Cloud Hybrid AI Architecture

### 8.1. Pemetaan Komponen UI ke Backend Services

| UI Component | Backend Service | D18 Reference |
|--------------|----------------|---------------|
| Chat Interface | BedrockChat.php | §6.1 Implementation |
| Model Selection | BedrockService | §5 Query Routing |
| Streaming Responses | SSE Controller | §7 Response Handling |
| Conversation Management | BedrockConversation Model | §6.4 Conversation Persistence |
| FAQ Suggestions | RagService | §6.2 Ollama Integration |
| Web-Augmented Display | DuckDuckGoService | §6.3 Web Search Integration |

### 8.2. Responsive Design untuk AI Interface

**Mobile-First AI Chat**:

```blade
{{-- Responsive AI chat layout --}}
<div class="ai-chat-responsive">
    {{-- Mobile: Full-screen chat --}}
    <div class="md:hidden fixed inset-0 bg-white z-50" x-show="mobileChat">
        <div class="flex flex-col h-full">
            <div class="chat-header p-4 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">{{ __('AI Assistant') }}</h2>
                    <button @click="mobileChat = false" class="p-2">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-hidden">
                <!-- Chat messages -->
            </div>
            <div class="chat-input p-4 border-t">
                <!-- Input area -->
            </div>
        </div>
    </div>

    {{-- Desktop: Sidebar or modal --}}
    <div class="hidden md:block">
        <!-- Desktop chat interface -->
    </div>
</div>
```

### 8.3. Pematuhan MyGOV Digital Service Standards

**AI Interface Compliance Checklist**:

- ✅ Bahasa Melayu sahaja (D15 v3.6.0)
- ✅ WCAG 2.2 AA accessibility
- ✅ Mobile-responsive design
- ✅ Government branding compliance
- ✅ Data privacy protection (PDPA 2010)
- ✅ Performance optimization (Core Web Vitals)
- ✅ Progressive enhancement
- ✅ Keyboard navigation support

---

## 9. Rujukan Silang dengan D00-D17

### 9.1. Pemetaan Keperluan AI Interface

| D-Doc | Section | AI Interface Requirement | Implementation |
|-------|---------|-------------------------|----------------|
| **D00** | §4.2 | True Hybrid Architecture support | AI chat available for guest and authenticated users |
| **D03** | SRS-AI-001 | Multi-model AI integration | Model selection interface with routing |
| **D04** | §6.3 | Streaming response architecture | SSE implementation with buffering |
| **D09** | §8.1 | AI conversation audit logging | Dual audit system for AI interactions |
| **D11** | §7.2 | Performance monitoring | Laravel Pulse integration for AI metrics |
| **D12** | §7.0 | AI interface design standards | This section (comprehensive UI/UX) |
| **D15** | §3.1 | Bahasa Melayu interface | All AI interface text in Malay |
| **D16** | §5.3 | Real-time AI notifications | WebSocket integration for streaming |
| **D17** | §6.2 | AI job queue management | Background processing for AI tasks |

### 9.2. Kebolehcapaian dan Pematuhan

**WCAG 2.2 AA Compliance Matrix**:

| Guideline | Level | AI Interface Implementation |
|-----------|-------|----------------------------|
| 1.1.1 Non-text Content | A | Alt text for AI status indicators, model icons |
| 1.3.1 Info and Relationships | A | Proper heading hierarchy, form labels |
| 1.4.3 Contrast (Minimum) | AA | 4.5:1 text contrast, 3:1 UI contrast |
| 1.4.11 Non-text Contrast | AA | Focus indicators, button states |
| 2.1.1 Keyboard | A | Full keyboard navigation support |
| 2.4.3 Focus Order | A | Logical tab order through interface |
| 3.2.2 On Input | A | No unexpected context changes |
| 4.1.2 Name, Role, Value | A | Proper ARIA labels and roles |

### 9.3. Prestasi dan Pengoptimuman

**Core Web Vitals Targets**:

- **Largest Contentful Paint (LCP)**: < 2.5s
- **First Input Delay (FID)**: < 100ms  
- **Cumulative Layout Shift (CLS)**: < 0.1
- **AI Streaming Latency**: < 200ms first chunk

**Implementation Strategies**:

- Virtual scrolling for conversation history
- Debounced input handling
- Progressive enhancement
- Efficient DOM updates during streaming
- Lazy loading of conversation data

---

## 10. Kesimpulan

Bahagian 7 ini menyediakan panduan komprehensif untuk reka bentuk antara muka AI yang mematuhi WCAG 2.2 AA dan terintegrasi dengan D18 Cloud Hybrid AI Architecture. Implementasi ini memastikan:

1. **Kebolehcapaian Penuh**: Sokongan pembaca skrin, navigasi papan kekunci, dan kontras warna yang mencukupi
2. **Prestasi Optimum**: Streaming responses, virtual scrolling, dan pengoptimuman DOM
3. **Pengalaman Pengguna Terbaik**: Antara muka responsif, visual feedback, dan pengurusan perbualan
4. **Pematuhan Standard**: MyGOV Digital Service Standards, PDPA 2010, dan ISO/IEC standards
5. **Integrasi Seamless**: Pemetaan langsung dengan backend services dari D18

Semua komponen AI interface mesti mengikuti panduan ini untuk memastikan konsistensi dan kualiti di seluruh sistem ICTServe v3.6.0.
