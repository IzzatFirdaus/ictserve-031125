---
name: user-table
description: Documentation for the UserTable Livewire component, accessibility notes and traceability
author: dev-team@motac.gov.my
trace: SRS-FR-012; D04 §4.2; D11 §6; D12 §3
last-updated: 2025-10-22
---

# UserTable (Livewire)

Purpose: Displays a searchable, paginated list of users for administrative use. Primary language: Bahasa Melayu (ms). Secondary: English (en).

Accessibility notes:
- Uses semantic table markup with <caption>, <th scope="col"> and descriptions
- Search input labelled; focus styles provided via Tailwind
- Pagination uses native links (Tailwind-styled)

How to test:
- Run php artisan test --filter UserTableTest
- Run axe or lighthouse against a preview environment and copy report to `user-table-axe-report.txt`

Traceability:
- Requirements: SRS-FR-012
- Design refs: D04 §4.2
