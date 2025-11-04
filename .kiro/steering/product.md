---
inclusion: always
description: "ICTServe product overview, core modules, target users, and compliance requirements"
version: "2.0.0"
last_updated: "2025-11-04"
---

# ICTServe Product Overview

ICTServe is an internal ICT service management system for Malaysia's Ministry of Tourism, Arts & Culture (MOTAC). It provides staff with a centralized platform for ICT service requests, issue reporting, and asset management.

## Core Modules

- **Helpdesk System**: Staff submit and track ICT support tickets with priority handling, SLA monitoring, and automated notifications
- **Asset Loan Management**: Request and manage ICT equipment loans with approval workflows and lifecycle tracking
- **Admin Dashboard**: Filament-powered admin panel for ICT staff to manage tickets, assets, and system operations

## Key Characteristics

- **Internal-only access**: Restricted to MOTAC staff and administrators
- **Role-based permissions**: Staff, admin, and superuser roles with Spatie permissions
- **Audit trail**: Complete change tracking for accountability and compliance
- **Bilingual support**: Malay (primary) and English with locale detection
- **WCAG 2.2 AA compliant**: Accessible design patterns throughout
- **Real-time notifications**: Laravel Reverb WebSocket server for live updates

## Target Users

- MOTAC staff (service requesters)
- ICT support staff (ticket handlers, asset managers)
- System administrators (configuration, security, audit oversight)

## Compliance

- PDPA 2010 (Malaysian data protection)
- WCAG 2.2 AA accessibility standards
- MyGOV Digital Service Standards v2.1.0
- ISO/IEC 12207, 15288, 29148 software engineering standards
