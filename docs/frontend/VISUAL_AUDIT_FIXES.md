# Visual Audit Findings & Fixes

**Date**: 2025-01-21  
**Audit Source**: Screenshot_21-11-2025 series (Screenshots 1-8)  
**Status**: 🔴 Critical Compliance Gaps Identified → ✅ Fixes Integrated  
**Documents Updated**: requirements.md (v1.2), design.md (v2.2), tasks.md (v2.2)

---

## Executive Summary

Visual audit of implemented screens revealed **critical compliance gaps** and **UX inconsistencies** between the Helpdesk and Asset Loan forms, as well as fragmented authentication interfaces. All findings have been documented and fixes integrated into specifications.

---

## 🔴 Critical Findings

### 1. Helpdesk Form ISO Compliance Gap

**Finding**: Helpdesk form (Screenshot 7) is **missing ISO document ID** `PK.(S).MOTAC.07.(L1)` in top-right corner.

**Comparison**:

- ✅ **Asset Loan Form** (Screenshot 8): Correctly displays `PK.(S).MOTAC.07.(L3)`
- ❌ **Helpdesk Form** (Screenshot 7): ISO header completely missing

**Impact**: **HIGH** - Government compliance violation

**Root Cause**: ISO header component not applied to Helpdesk form during implementation

**Fix**:

- **Task 3.1.12**: Verify ISO Header Display - Ensure PK.(S).MOTAC.07.(L1) visible
- **Design Update**: Added ISO header component specification with exact styling

---

### 2. Helpdesk Declaration Text Incorrect

**Finding**: Helpdesk form uses **generic declaration text** instead of exact legacy legal text.

**Current Text** (Screenshot 7):

> "...maklumat yang diberikan adalah benar dan tepat"

**Required Text** (Legacy System):

> "Saya memperakui dan mengesahkan bahawa semua maklumat yang diberikan di dalam **eBorang Laporan Kerosakan** ini adalah benar dan tepat..."

**Impact**: **HIGH** - Legal compliance violation

**Root Cause**: Generic text used instead of exact legacy legal disclaimer

**Fix**:

- **Task 3.1.13**: Update Declaration Text - Replace with exact legacy text
- **Design Update**: Documented exact Malay text with "eBorang Laporan Kerosakan" reference

---

### 3. Searchable Division Select Implementation

**Finding**: "Bahagian" dropdown shows "Pilih bahagian" but implementation unclear.

**Risk**: If implemented as native HTML `<select>`, will be **unusable** with 100+ divisions.

**Required**: Virtual scrolled searchable combobox (Livewire/Alpine component)

**Impact**: **MEDIUM** - Usability issue for large lists

**Fix**:

- **Task 3.1.10**: Explicitly specifies "NOT native HTML select"
- **Design Update**: Added ARIA combobox pattern requirement

---

## ⚠️ UX Inconsistencies

### 4. Login Screen Fragmentation

**Finding**: Two visually distinct login screens with inconsistent styling.

**Observations**:

- **Screenshot 5**: Malay interface ("Log masuk ke akaun anda") - specific styling
- **Screenshot 6**: English interface ("Log in") - different field spacing
- **Both**: Missing language switcher on login screen

**Impact**: **MEDIUM** - User confusion, inconsistent branding

**Root Cause**: Separate Admin and Staff login views

**Fix**:

- **Task 4.0.1**: Merge Admin and Staff Login Views
- **Task 4.0.2**: Add Language Switcher to Login
- **Task 4.0.3**: Standardize Login Styling
- **Task 4.0.4**: Implement Role-Based Redirect
- **Design Update**: Complete unified login specification with code examples

---

### 5. Contact Form Dead-End

**Finding**: "Hantar Mesej Kepada Kami" form (Screenshot 2) likely sends emails to ignored inbox.

**Risk**: User inquiries not tracked, no follow-up mechanism.

**Impact**: **MEDIUM** - Poor user experience, lost inquiries

**Solution**: Route Contact submissions to Helpdesk as "General Enquiry" tickets

**Fix**:

- **Task 3.3.6**: Implement Contact Form Integration
- **Task 3.3.7**: Return Ticket ID on Contact Submission
- **Design Update**: Complete Contact form routing specification with code

---

### 6. Service Request Scope Ambiguity

**Finding**: "Permintaan Perkhidmatan" card (Screenshot 3) displayed but workflow unclear.

**Risk**: Dead-end landing page if backend logic not defined.

**Impact**: **LOW** - Potential user confusion

**Solution**: Route to Helpdesk form with pre-filled "Service Request" category

**Fix**:

- **Task 3.3.8**: Implement Service Request Routing
- **Design Update**: Service Request routing specification with code

---

### 7. Footer Contrast Issue

**Finding**: Footer text (Screenshot 1) appears gray on dark blue background.

**Risk**: May not meet WCAG 4.5:1 contrast requirement.

**Impact**: **LOW** - Accessibility concern

**Fix**:

- **Design Update**: Added footer contrast verification note
- **Recommendation**: Test with contrast checker, adjust colors if needed

---

## 📋 Document Updates Summary

### requirements.md (v1.1 → v1.2)

**New Requirements**:

- **Requirement 21**: Contact Form and Service Request Integration (5 criteria)
- **Requirement 22**: Unified Authentication Interface (5 criteria)

**Total New Criteria**: 10

### design.md (v2.1 → v2.2)

**New Sections**:

- **Contact Form and Service Request Integration**: Complete routing logic with code examples
- **Unified Login Layout**: Single login specification with role-based redirect

**Critical Fixes**:

- **Helpdesk Form Compliance**: Added ISO header component, exact legal text, searchable select requirements
- **Footer Contrast**: Added WCAG verification note

**Code Examples Added**: 4 (Contact routing, Service Request routing, Unified login, Role-based redirect)

### tasks.md (v2.1 → v2.2)

**New Tasks**:

**Phase 3 (Guest Forms)**:

- Task 3.1.12: **FIX** - Verify ISO Header Display
- Task 3.1.13: **FIX** - Update Declaration Text
- Task 3.3.6: Implement Contact Form Integration
- Task 3.3.7: Return Ticket ID on Contact Submission
- Task 3.3.8: Implement Service Request Routing

**Phase 4 (Authentication)**:

- Task 4.0.1: Merge Admin and Staff Login Views
- Task 4.0.2: Add Language Switcher to Login
- Task 4.0.3: Standardize Login Styling
- Task 4.0.4: Implement Role-Based Redirect

**Total New Tasks**: 9 (2 critical fixes, 7 enhancements)

---

## 🎯 Priority Matrix

| Finding                      | Severity    | Compliance Impact    | User Impact | Priority |
| ---------------------------- | ----------- | -------------------- | ----------- | -------- |
| 1. Missing ISO Header        | 🔴 Critical | Government violation | Low         | **P0**   |
| 2. Incorrect Declaration     | 🔴 Critical | Legal violation      | Low         | **P0**   |
| 3. Division Select           | 🟡 Medium   | None                 | High        | **P1**   |
| 4. Login Fragmentation       | 🟡 Medium   | None                 | Medium      | **P1**   |
| 5. Contact Form Dead-End     | 🟡 Medium   | None                 | Medium      | **P1**   |
| 6. Service Request Ambiguity | 🟢 Low      | None                 | Low         | **P2**   |
| 7. Footer Contrast           | 🟢 Low      | WCAG concern         | Low         | **P2**   |

---

## ✅ Fix Implementation Checklist

### Immediate Actions (P0 - Critical)

- [ ] **Task 3.1.12**: Add ISO header `PK.(S).MOTAC.07.(L1)` to Helpdesk form
- [ ] **Task 3.1.13**: Replace declaration text with exact legacy legal text
- [ ] **Verify**: ISO header matches Asset Loan form styling
- [ ] **Verify**: Declaration includes "eBorang Laporan Kerosakan" reference

### High Priority (P1)

- [ ] **Task 3.1.10**: Confirm Division select is virtual scrolled combobox (NOT native select)
- [ ] **Task 4.0.1-4.0.4**: Implement unified login with language switcher
- [ ] **Task 3.3.6-3.3.7**: Route Contact form to Helpdesk with Ticket ID return
- [ ] **Verify**: Login screen consistent across all devices
- [ ] **Verify**: Contact submissions create trackable tickets

### Medium Priority (P2)

- [ ] **Task 3.3.8**: Route Service Request card to Helpdesk form
- [ ] **Footer Contrast**: Test with contrast checker, adjust if needed
- [ ] **Verify**: All support requests tracked in unified system

---

## 📊 Compliance Coverage After Fixes

### ISO Document Standards

| Form       | ISO ID               | Display Location | Before     | After        |
| ---------- | -------------------- | ---------------- | ---------- | ------------ |
| Helpdesk   | PK.(S).MOTAC.07.(L1) | Top-right corner | ❌ Missing | ✅ Specified |
| Asset Loan | PK.(S).MOTAC.07.(L3) | Top-right corner | ✅ Correct | ✅ Correct   |

### Legal Disclaimers

| Form       | Disclaimer Type  | Before          | After                |
| ---------- | ---------------- | --------------- | -------------------- |
| Helpdesk   | Perakuan (Malay) | ❌ Generic text | ✅ Exact legacy text |
| Asset Loan | 11 T&C Rules     | ✅ Correct      | ✅ Correct           |

### UX Consistency

| Component         | Before                     | After                  |
| ----------------- | -------------------------- | ---------------------- |
| Login Screens     | ❌ Fragmented (2 versions) | ✅ Unified (1 version) |
| Language Switcher | ❌ Missing on login        | ✅ Visible on login    |
| Contact Form      | ❌ Dead-end email          | ✅ Tracked ticket      |
| Service Request   | ❌ Ambiguous routing       | ✅ Clear routing       |

---

## 🔍 Testing Requirements

### Compliance Testing

**Helpdesk Form**:

- [ ] Verify ISO header `PK.(S).MOTAC.07.(L1)` displays in top-right corner
- [ ] Verify declaration text matches exact legacy text (includes "eBorang Laporan Kerosakan")
- [ ] Verify Division select is searchable combobox (NOT native select)
- [ ] Test with 100+ divisions to ensure performance

**Login Screen**:

- [ ] Verify single login interface for all roles
- [ ] Verify language switcher visible and functional
- [ ] Verify consistent styling across devices
- [ ] Test role-based redirect (Admin → Filament, Staff → Portal)

**Contact & Service Request**:

- [ ] Verify Contact form creates Helpdesk ticket
- [ ] Verify Ticket ID displayed after submission
- [ ] Verify Service Request routes to Helpdesk form
- [ ] Verify category pre-filled correctly

### Visual Regression Testing

- [ ] Compare Helpdesk form header with Asset Loan form header (must match)
- [ ] Verify footer contrast meets WCAG 4.5:1 ratio
- [ ] Test login screen consistency across browsers
- [ ] Verify responsive behavior on mobile devices

---

## 📈 Impact Analysis

### Development Effort

| Phase     | Original Tasks | New Tasks   | % Increase |
| --------- | -------------- | ----------- | ---------- |
| Phase 3   | 22             | 27 (+5)     | +23%       |
| Phase 4   | 28             | 32 (+4)     | +14%       |
| **Total** | **50**         | **59 (+9)** | **+18%**   |

### Timeline Impact

- **Original Timeline**: 20 weeks
- **Additional Effort**: ~1 week (compliance fixes + UX improvements)
- **Revised Timeline**: 21 weeks
- **Critical Path Impact**: Minimal (fixes in parallel phases)

### Risk Mitigation

| Risk                            | Before    | After        |
| ------------------------------- | --------- | ------------ |
| Government compliance violation | 🔴 High   | ✅ Mitigated |
| Legal text incorrect            | 🔴 High   | ✅ Mitigated |
| User confusion (login)          | 🟡 Medium | ✅ Mitigated |
| Lost inquiries (contact)        | 🟡 Medium | ✅ Mitigated |
| Unusable division select        | 🟡 Medium | ✅ Mitigated |

---

## 🚀 Next Steps

### Immediate Actions

1. **Review Fixes**: Stakeholder review of visual audit findings and proposed fixes
2. **Prioritize P0 Tasks**: Schedule Tasks 3.1.12-3.1.13 for immediate implementation
3. **Validate Legal Text**: Confirm exact Malay text with legal/compliance team
4. **Test Division Select**: Verify current implementation is searchable combobox

### Implementation Priority

**Critical (This Sprint)**:

- Task 3.1.12: Add ISO header to Helpdesk form
- Task 3.1.13: Update declaration text

**High Priority (Next Sprint)**:

- Task 4.0.1-4.0.4: Unified login implementation
- Task 3.3.6-3.3.7: Contact form integration

**Medium Priority (Following Sprint)**:

- Task 3.3.8: Service Request routing
- Footer contrast verification

### Quality Assurance

**Visual Regression Tests**:

- [ ] Create baseline screenshots of corrected Helpdesk form
- [ ] Create baseline screenshots of unified login
- [ ] Implement automated visual regression tests

**Compliance Verification**:

- [ ] ISO header display verification
- [ ] Legal text accuracy verification
- [ ] WCAG contrast verification

---

## 📝 Conclusion

Visual audit identified **2 critical compliance gaps** (missing ISO header, incorrect legal text) and **5 UX inconsistencies** (login fragmentation, contact dead-end, service request ambiguity, division select, footer contrast).

All findings have been:

1. ✅ Documented with severity and impact analysis
2. ✅ Integrated into specifications (requirements, design, tasks)
3. ✅ Prioritized with clear implementation checklist
4. ✅ Assigned to specific tasks with traceability

The specifications now provide **complete coverage** of visual audit findings with clear fixes and testing requirements.

---

**Document Version**: 1.0  
**Last Updated**: 2025-01-21  
**Author**: Frontend Engineering Team  
**Status**: ✅ Complete - All Findings Documented and Fixes Integrated  
**Next Review**: After P0 tasks implementation
