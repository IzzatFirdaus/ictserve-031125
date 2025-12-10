# FAQ Accordion Enhancement - Requirements Addendum

## Introduction

This addendum extends **Requirement 2: Guest-Only Forms with Enhanced Accessibility** from the Unified Frontend Pages Redesign specification to provide comprehensQ content for the ICTServe welcome page. The FAQ section serves as a critical self-service resource for users to understand system capabilities, processes, and policies before submitting tickets or loan applications.

## Glossary

- **FAQ_Accordion**: Expandable/collapsible question-answer interface using Alpine.js for state management
- **Self_Service_Content**: Information that enables users to resolve queries without contacting support
- **Progressive_Disclosure**: UX pattern revealing information only when needed to reduce cognitive load
- **WCAG_2_2_SC_2_4_11**: Focus Not Obscured - ensures focused elements remain visible during interaction

## Requirements

### Requirement 2.1: Comprehensive FAQ Content Coverage

**User Story:** As a user visiting the ICTServe welcome page, I want comprehensive FAQ content covering common questions about helpdesk tickets and asset loans, so that I can understand system capabilities and processes before submitting requests.

#### Acceptance Criteria

1. THE ICTServe_System SHALL provide FAQ content covering helpdesk ticket submission process, asset loan application process, status tracking, approval workflows, and contact information
2. THE ICTServe_System SHALL organize FAQ items in logical sequence: submission processes first, followed by tracking, approvals, and support contact
3. THE ICTServe_System SHALL provide bilingual FAQ content (Bahasa Melayu primary, English secondary) with accurate translations
4. THE ICTServe_System SHALL include minimum 6 FAQ items covering: ticket submission, loan application, status checking, approval timeline, asset return, and support contact
5. THE ICTServe_System SHALL update FAQ content to reflect current system capabilities including email-based workflows and dual approval methods

### Requirement 2.2: Accessible FAQ Accordion Implementation

**User Story:** As a user with accessibility needs, I want an accessible FAQ accordion that supports keyboard navigation, screen readers, and focus management, so that I can access FAQ content regardless of my interaction method.

#### Acceptance Criteria

1. WHEN a user interacts with FAQ accordion, THE ICTServe_System SHALL implement WCAG 2.2 Level AA compliance with proper ARIA attributes (aria-expanded, aria-controls)
2. WHEN a user navigates FAQ accordion via keyboard, THE ICTServe_System SHALL provide full keyboard support with Enter/Space to toggle, Tab to navigate, and visible focus indicators (3px outline, 2px offset, 3:1 contrast)
3. WHEN a user expands FAQ item, THE ICTServe_System SHALL ensure focus remains visible per WCAG 2.2 SC 2.4.11 (Focus Not Obscured)
4. WHEN a user accesses FAQ with screen reader, THE ICTServe_System SHALL announce state changes using proper ARIA live regions and semantic button elements
5. THE ICTServe_System SHALL implement smooth expand/collapse animations using Alpine.js x-collapse directive with 200ms duration

### Requirement 2.3: FAQ Visual Design and Branding

**User Story:** As a user viewing the FAQ section, I want visually consistent design that matches MOTAC branding and maintains readability, so that I can easily scan and read FAQ content.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement FAQ accordion using Compliant_Color_Palette with gray-50/gray-700 backgrounds and primary-500 focus rings
2. THE ICTServe_System SHALL provide 4.5:1 minimum text contrast for FAQ questions (text-gray-900/gray-100) and answers (text-gray-600/gray-300)
3. THE ICTServe_System SHALL use font-heading for FAQ questions (font-semibold, text-lg) and font-sans for answers (leading-relaxed)
4. THE ICTServe_System SHALL implement 44×44px minimum touch targets for FAQ toggle buttons per WCAG 2.2 SC 2.5.8
5. THE ICTServe_System SHALL provide visual feedback for hover states (bg-gray-100/gray-600) and smooth chevron rotation (180deg) for expanded items

### Requirement 2.4: FAQ Content Maintenance and Localization

**User Story:** As a content administrator, I want maintainable FAQ content with proper localization support, so that I can easily update FAQ items and ensure accurate translations.

#### Acceptance Criteria

1. THE ICTServe_System SHALL implement FAQ content using Laravel localization system with lang/ms/welcome.php and lang/en/welcome.php files
2. THE ICTServe_System SHALL provide translation keys following naming convention: welcome.faq.q{number}.question and welcome.faq.q{number}.answer
3. THE ICTServe_System SHALL validate FAQ translations for completeness ensuring all keys exist in both languages
4. THE ICTServe_System SHALL support dynamic FAQ content updates without requiring code changes through localization files
5. THE ICTServe_System SHALL maintain FAQ content version history and update timestamps in localization file comments

## Proposed FAQ Content

### FAQ Items (Bilingual)

#### Q1: Bagaimana cara untuk membuat aduan ICT? / How do I submit an ICT complaint?
**MS**: Anda boleh membuat aduan ICT dengan mengklik butang "Buat Aduan" di halaman utama. Isi borang dengan maklumat lengkap mengenai masalah yang dihadapi. Tiket aduan akan dijana dan anda akan menerima nombor rujukan melalui emel dalam masa 60 saat.

**EN**: You can submit an ICT complaint by clicking the "Submit Complaint" button on the main page. Fill in the form with complete information about the issue. A ticket will be generated and you will receive a reference number via email within 60 seconds.

#### Q2: Bagaimana cara memohon pinjaman aset ICT? / How do I apply for ICT asset loans?
**MS**: Klik butang "Mohon Pinjaman" dan lengkapkan borang permohonan dengan maklumat peminjam, tarikh pinjaman, dan jenis aset yang diperlukan. Permohonan anda akan dihantar kepada pegawai kelulusan (Gred 41 ke atas) untuk kelulusan. Anda akan menerima emel pengesahan dalam masa 60 saat.

**EN**: Click the "Apply for Loan" button and complete the application form with borrower information, loan dates, and required asset type. Your application will be sent to approval officers (Grade 41 and above) for approval. You will receive a confirmation email within 60 seconds.

#### Q3: Bagaimana saya boleh menyemak status permohonan saya? / How can I check my application status?
**MS**: Anda boleh menyemak status dengan memasukkan nombor rujukan (contoh: HD-2024-001234 untuk tiket atau LA-2024-005678 untuk pinjaman) di bahagian "Semak Status" di halaman utama. Anda juga boleh menggunakan pautan penjejakan yang dihantar melalui emel.

**EN**: You can check the status by entering your reference number (example: HD-2024-001234 for tickets or LA-2024-005678 for loans) in the "Check Status" section on the main page. You can also use the tracking link sent via email.

#### Q4: Berapa lama masa yang diperlukan untuk kelulusan pinjaman aset? / How long does asset loan approval take?
**MS**: Permohonan pinjaman aset memerlukan kelulusan daripada pegawai Gred 41 ke atas. Pegawai kelulusan boleh meluluskan melalui pautan emel (tanpa log masuk) atau melalui portal (dengan log masuk). Masa kelulusan bergantung kepada ketersediaan pegawai kelulusan, tetapi sistem akan menghantar peringatan automatik selepas 48 jam jika tiada tindakan diambil.

**EN**: Asset loan applications require approval from Grade 41 and above officers. Approval officers can approve via email link (without login) or through the portal (with login). Approval time depends on officer availability, but the system will send automatic reminders after 48 hours if no action is taken.

#### Q5: Apa yang perlu saya lakukan selepas menggunakan aset yang dipinjam? / What should I do after using borrowed assets?
**MS**: Selepas menggunakan aset, anda perlu memulangkan aset kepada admin ICT dalam keadaan baik. Admin akan memeriksa keadaan aset dan merekodkan pemulangan dalam sistem. Jika aset rosak, tiket penyelenggaraan automatik akan dibuat dalam masa 5 saat untuk tindakan pembaikan.

**EN**: After using the asset, you must return it to the ICT admin in good condition. The admin will inspect the asset condition and record the return in the system. If the asset is damaged, an automatic maintenance ticket will be created within 5 seconds for repair action.

#### Q6: Siapa yang boleh saya hubungi jika saya memerlukan bantuan? / Who can I contact if I need help?
**MS**: Untuk bantuan teknikal atau pertanyaan mengenai sistem, anda boleh menghubungi Bahagian Pengurusan Maklumat (BPM) MOTAC melalui emel di <ictserve@motac.gov.my> atau telefon di 03-XXXX XXXX (waktu pejabat: 8:00 AM - 5:00 PM, Isnin - Jumaat). Anda juga boleh membuat tiket aduan melalui sistem untuk bantuan teknikal.

**EN**: For technical assistance or system inquiries, you can contact the Information Management Division (BPM) MOTAC via email at <ictserve@motac.gov.my> or phone at 03-XXXX XXXX (office hours: 8:00 AM - 5:00 PM, Monday - Friday). You can also submit a complaint ticket through the system for technical assistance.

## Technical Implementation Notes

### Alpine.js State Management

```javascript
x-data="{ openFaq: null }"
@click="openFaq = openFaq === 1 ? null : 1"
x-show="openFaq === 1"
x-collapse
```

### ARIA Attributes

```html
aria-expanded="openFaq === 1"
aria-controls="faq-answer-1"
role="button"
```

### Focus Management

```css
focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset
```

### Responsive Design

- Mobile (320px-414px): Full-width accordion, 16px padding
- Tablet (768px-1024px): Max-width 768px, 24px padding
- Desktop (1280px+): Max-width 1024px, centered

## Success Criteria

The FAQ accordion enhancement will be considered successful when:

1. **Content Coverage**: Minimum 6 FAQ items covering all major user journeys (ticket submission, loan application, status tracking, approvals, returns, support)
2. **Accessibility**: 100% WCAG 2.2 Level AA compliance with keyboard navigation, screen reader support, and focus management
3. **Bilingual Support**: Complete translations in Bahasa Melayu and English with accurate content
4. **User Experience**: Smooth animations, clear visual feedback, and intuitive interaction patterns
5. **Maintainability**: Localization-based content management enabling easy updates without code changes

## Traceability

- **D03 Software Requirements**: Requirement 2 (Guest-Only Forms), Requirement 8 (Bilingual Support)
- **D04 Software Design**: Component architecture, Alpine.js patterns, localization system
- **D12 UI/UX Design Guide**: FAQ accordion patterns, progressive disclosure
- **D14 UI/UX Style Guide**: MOTAC branding, compliant color palette, typography
- **D15 Language Support**: Bilingual implementation, localization file structure

---

**Document Version**: 1.0  
**Created**: 2025-12-09  
**Author**: Frontend Engineering Team  
**Status**: Ready for Implementation  
**Parent Spec**: frontend-pages-redesign
