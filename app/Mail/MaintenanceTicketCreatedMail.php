<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Maintenance Ticket Created Email (Alias)
 *
 * Alias for MaintenanceTicketNotification for naming consistency.
 * Sent to maintenance team when an asset is returned damaged.
 *
 * @component Email Template
 *
 * @description WCAG 2.2 AA compliant email notification for automatic maintenance ticket creation
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-002.3 Asset damage reporting
 * @trace D03-FR-008.4 Cross-module notifications
 * @trace Requirements 2.3, 8.4, 10.3
 *
 * @wcag_level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class MaintenanceTicketCreatedMail extends MaintenanceTicketNotification
{
    // This class extends MaintenanceTicketNotification for naming consistency
    // All functionality is inherited from the parent class
}
