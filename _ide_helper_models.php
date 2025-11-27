<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Enhanced Asset Model with Cross-Module Integration
 * 
 * Comprehensive asset tracking with maintenance integration and cross-module
 * connectivity with the helpdesk system.
 *
 * @see D03-FR-003.1 Asset inventory management
 * @see D03-FR-016.2 Cross-module integration
 * @see D03-FR-018.1 Asset lifecycle management
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property string $asset_tag
 * @property string $name
 * @property string $brand
 * @property string $model
 * @property string|null $serial_number
 * @property int $category_id
 * @property array|null $specifications
 * @property \Carbon\Carbon $purchase_date
 * @property float $purchase_value
 * @property float $current_value
 * @property AssetStatus $status
 * @property string $location
 * @property AssetCondition $condition
 * @property array<string, mixed>|null $accessories
 * @property \Carbon\Carbon|null $warranty_expiry
 * @property \Carbon\Carbon|null $last_maintenance_date
 * @property \Carbon\Carbon|null $next_maintenance_date
 * @property int $maintenance_tickets_count
 * @property array<string, mixed>|null $loan_history_summary
 * @property array<string, mixed>|null $availability_calendar
 * @property array<string, mixed>|null $utilization_metrics
 * @property array<string, mixed>|null $specifications
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\AssetCategory $category
 * @property-read \App\Models\LoanApplication|null $currentLoan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanItem> $loanItems
 * @property-read int|null $loan_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $maintenanceRecords
 * @property-read int|null $maintenance_records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset availableForLoan(string $startDate, string $endDate)
 * @method static \Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset requiringMaintenance()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAssetTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereAvailabilityCalendar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCurrentValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLastMaintenanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLoanHistorySummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereMaintenanceTicketsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNextMaintenanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset wherePurchaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSpecifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUtilizationMetrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereWarrantyExpiry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withHelpdeskHistory()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withoutTrashed()
 */
	class Asset extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Asset Category Model
 * 
 * Defines categories for ICT equipment with custom specification templates.
 *
 * @see D03-FR-018.2 Asset categorization system
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property array<string, mixed>|null $specification_template
 * @property int $default_loan_duration_days
 * @property int $max_loan_duration_days
 * @property bool $requires_approval
 * @property bool $is_active
 * @property int $sort_order
 * @property array<array-key, mixed>|null $default_accessories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\AssetCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDefaultAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDefaultLoanDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereMaxLoanDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereRequiresApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereSpecificationTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withoutTrashed()
 */
	class AssetCategory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $loan_application_id
 * @property string $type
 * @property int|null $user_id
 * @property int|null $processed_by
 * @property string|null $condition_before
 * @property string|null $condition_after
 * @property array<array-key, mixed>|null $accessories
 * @property string|null $notes
 * @property string|null $damage_description
 * @property string|null $location_from
 * @property string|null $location_to
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \App\Models\User|null $processedByUser
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereDamageDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLocationFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereLocationTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction whereUserId($value)
 */
	class AssetTransaction extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Enhanced Audit Model for ICTServe Compliance
 * 
 * Extends Laravel Auditing with 7-year retention policy,
 * immutable storage, and enhanced search capabilities.
 *
 * @see D03-FR-010.2 Audit logging system
 * @see D09 Database Documentation - Audit requirements
 * @see D11 Technical Design - Compliance standards
 * @property int $id
 * @property string $user_type
 * @property int|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property int $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property array<string, mixed>|null $old_values
 * @property array<string, mixed>|null $new_values
 * @property-read \Illuminate\Database\Eloquent\Model $auditable
 * @property-read string $changes_summary
 * @property-read string $user_info
 * @property-read \Illuminate\Database\Eloquent\Model|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit byAuditableType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit byEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit byUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit dateRange(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit expired()
 * @method static \Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit securityEvents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUserType($value)
 */
	class Audit extends \Eloquent {}
}

namespace App\Models{
/**
 * Blocked IP Model
 * 
 * Stores IP addresses blocked for abuse prevention.
 * Supports both manual (admin) and automatic (rate limit violation) blocking.
 *
 * @property int $id
 * @property string $ip_address
 * @property string|null $reason
 * @property string $type
 * @property int $violation_count
 * @property \Carbon\Carbon $blocked_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $blocked_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $blockedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereBlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereBlockedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BlockedIp whereViolationCount($value)
 */
	class BlockedIp extends \Eloquent {}
}

namespace App\Models{
/**
 * Cross Module Integration Model
 *
 * @property int $id
 * @property int $helpdesk_ticket_id
 * @property int $loan_application_id
 * @property string $integration_type
 * @property string $trigger_event
 * @property array $integration_data
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $processed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication|null $assetLoan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket|null $helpdeskTicket
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \App\Models\User|null $processedBy
 * @method static \Database\Factories\CrossModuleIntegrationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration processed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration triggeredBy(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration unprocessed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereHelpdeskTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereIntegrationData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereIntegrationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereTriggerEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration withoutTrashed()
 */
	class CrossModuleIntegration extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name_ms
 * @property string $name_en
 * @property string|null $description_ms
 * @property string|null $description_en
 * @property int|null $parent_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Division> $children
 * @property-read int|null $children_count
 * @property-read string|null $description
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read Division|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescriptionMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withoutTrashed()
 */
	class Division extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Email Log Model
 * 
 * Tracks email delivery status, retry attempts, performance metrics, and
 * unified notification system integration (multi-channel tracking).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $recipient_email
 * @property string $subject
 * @property string $email_type
 * @property string $status
 * @property array|null $data
 * @property int $retry_attempts
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $last_retry_at
 * @property string|null $error_message
 * @property array|null $channels Multi-channel dispatch tracking
 * @property string|null $notification_type From config/notifications.php
 * @property string|null $priority critical/high/normal/low
 * @property \Carbon\Carbon|null $next_retry_at Scheduled retry time
 * @property string|null $final_status delivered/permanently_failed/bounced/rejected
 * @property bool $preference_bypassed User preference override flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string|null $recipient_name
 * @property string $mailable_class
 * @property string|null $message_id
 * @property string|null $status_message
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon $queued_at
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog delivered()
 * @method static \Database\Factories\EmailLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog ofType(string $notificationType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog permanentlyFailed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog preferenceBypassed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog retryable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereFinalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereMailableClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereNextRetryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog wherePreferenceBypassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereQueuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereRecipientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereRecipientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereStatusMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog withPriority(string $priority)
 */
	class EmailLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string $locale
 * @property string $subject
 * @property string $body_html
 * @property string|null $body_text
 * @property array<array-key, mixed>|null $variables
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate forCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate forLocale(string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereBodyHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereBodyText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereVariables($value)
 */
	class EmailTemplate extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name_ms
 * @property string $name_en
 * @property int $level
 * @property bool $can_approve_loans
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCanApproveLoans($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 */
	class Grade extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $helpdesk_ticket_id
 * @property int|null $user_id
 * @property string $filename
 * @property string $original_filename
 * @property string $mime_type
 * @property int $file_size
 * @property string $file_path
 * @property string $disk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereHelpdeskTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withoutTrashed()
 */
	class HelpdeskAttachment extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $helpdesk_ticket_id
 * @property int|null $user_id
 * @property string|null $commenter_name
 * @property string|null $commenter_email
 * @property string $comment
 * @property bool $is_internal
 * @property bool $is_resolution
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCommenterEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCommenterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereHelpdeskTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereIsResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withoutTrashed()
 */
	class HelpdeskComment extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * HelpdeskTicket Model - Enhanced with Hybrid Architecture Support
 * 
 * Supports both guest submissions (no user_id) and authenticated submissions (with user_id).
 * Integrates with asset loan system for cross-module functionality.
 *
 * @see D03 Software Requirements Specification - Requirement 1, 2
 * @see D04 Software Design Document - Hybrid Architecture
 * @see D09 Database Documentation - helpdesk_tickets table
 * @property string|null $guest_email
 * @property int $id
 * @property string $ticket_number
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property string|null $guest_phone
 * @property string|null $guest_grade
 * @property string|null $guest_division
 * @property string|null $guest_staff_id
 * @property string|null $job_grade
 * @property string|null $staff_id
 * @property int|null $division_id
 * @property int $category_id
 * @property string $priority
 * @property string $subject
 * @property string $description
 * @property string|null $damage_type
 * @property bool $declaration_accepted
 * @property string|null $internal_notes
 * @property string $status
 * @property int|null $assigned_to_division
 * @property string|null $assigned_to_agency
 * @property int|null $assigned_to_user
 * @property int|null $asset_id
 * @property int|null $related_loan_application_id
 * @property \Illuminate\Support\Carbon|null $sla_response_due_at
 * @property \Illuminate\Support\Carbon|null $sla_resolution_due_at
 * @property \Illuminate\Support\Carbon|null $responded_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property int $escalation_level
 * @property \Illuminate\Support\Carbon|null $escalation_notified_at
 * @property \Illuminate\Support\Carbon|null $sla_breached_at
 * @property string|null $sla_breach_type
 * @property \Illuminate\Support\Carbon|null $sla_paused_at
 * @property string|null $sla_pause_reason
 * @property int $sla_total_paused_hours
 * @property string|null $closure_reason
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property string|null $admin_notes
 * @property string|null $resolution_notes
 * @property string|null $anonymized_at
 * @property string|null $claimed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $assetLoanApplications
 * @property-read int|null $asset_loan_applications_count
 * @property-read \App\Models\Division|null $assignedDivision
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $auditTrail
 * @property-read int|null $audit_trail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\TicketCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CrossModuleIntegration> $crossModuleIntegrations
 * @property-read int|null $cross_module_integrations_count
 * @property-read \App\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \App\Models\HelpdeskComment|null $latestComment
 * @property-read \App\Models\Asset|null $relatedAsset
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\HelpdeskTicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket optimizedCount()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket optimizedPagination(int $perPage = 25)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAnonymizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssignedToAgency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssignedToDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssignedToUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereClaimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereClosureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDamageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDeclarationAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereEscalationLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereEscalationNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereFirstResponseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereGuestStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereInternalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereJobGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereRelatedLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaBreachType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaBreachedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaPauseReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaPausedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaResolutionDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaResponseDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaTotalPausedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereTicketNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withCommonRelations()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withoutTrashed()
 */
	class HelpdeskTicket extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int|null $parent_id
 * @property string $comment Comment text (max 1000 characters enforced at application level)
 * @property array<array-key, mixed>|null $mentions Array of mentioned user IDs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $commentable
 * @property-read string $formatted_comment
 * @property-read InternalComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InternalComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\InternalCommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment topLevel()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereMentions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment withMentionFor(int $userId)
 */
	class InternalComment extends \Eloquent {}
}

namespace App\Models{
/**
 * Enhanced Loan Application Model with ICTServe Integration
 * 
 * Supports hybrid architecture (guest + authenticated), email-based approval workflows,
 * and cross-module integration with helpdesk system.
 *
 * @see D03-FR-001.2 Hybrid architecture support
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property string $application_number
 * @property int|null $user_id
 * @property string $applicant_name
 * @property string $applicant_email
 * @property string $applicant_phone
 * @property string $staff_id
 * @property string $grade
 * @property int $division_id
 * @property string $purpose
 * @property string $location
 * @property string $return_location
 * @property \Carbon\Carbon $loan_start_date
 * @property \Carbon\Carbon $loan_end_date
 * @property LoanStatus $status
 * @property LoanPriority $priority
 * @property float $total_value
 * @property string|null $approver_email
 * @property string|null $approved_by_name
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $approval_token
 * @property \Carbon\Carbon|null $approval_token_expires_at
 * @property string|null $approval_method
 * @property string|null $approval_remarks
 * @property string|null $rejected_reason
 * @property string|null $special_instructions
 * @property array<string, mixed>|null $related_helpdesk_tickets
 * @property bool $maintenance_required
 * @property string|null $tracking_token
 * @property string|null $tracking_token_expires_at
 * @property string $applicant_position
 * @property string $applicant_grade
 * @property \Illuminate\Support\Carbon $expected_return_date
 * @property string|null $pickup_otp_hash
 * @property \Illuminate\Support\Carbon|null $pickup_otp_expires_at
 * @property int $pickup_otp_attempts
 * @property \Illuminate\Support\Carbon|null $pickup_otp_generated_at
 * @property \Illuminate\Support\Carbon|null $pickup_otp_validated_at
 * @property bool $is_applicant_responsible
 * @property bool $is_delegate True if application submitted on behalf of another staff member
 * @property array<array-key, mixed>|null $responsible_officer_details JSON: {name, position, grade, email, phone, staff_id, division_id} for delegation workflow
 * @property string|null $responsible_officer_name
 * @property string|null $responsible_officer_position
 * @property string|null $responsible_officer_grade
 * @property string|null $responsible_officer_phone
 * @property string|null $responsible_officer_email
 * @property \Illuminate\Support\Carbon|null $responsible_officer_acknowledged_at
 * @property string|null $sponsorship_token
 * @property \Illuminate\Support\Carbon|null $sponsorship_token_expires_at
 * @property \Illuminate\Support\Carbon|null $applicant_declaration_date
 * @property string|null $applicant_digital_signature
 * @property bool $terms_acknowledged
 * @property \Illuminate\Support\Carbon|null $declared_at
 * @property int|null $approver_id
 * @property string $approval_status
 * @property \Illuminate\Support\Carbon|null $approval_date
 * @property string|null $approver_digital_signature
 * @property string|null $approval_notes
 * @property array<array-key, mixed>|null $accessories
 * @property string|null $anonymized_at
 * @property string|null $claimed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $pickup_otp_validated_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Division $division
 * @property-read \Illuminate\Database\Eloquent\Relations\HasMany<LoanItem, LoanApplication> $items
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanItem> $loanItems
 * @property-read int|null $loan_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereAnonymizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantDeclarationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantDigitalSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovalTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovedByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApproverDigitalSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApproverEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApproverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereClaimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereDeclaredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereExpectedReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereIsApplicantResponsible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereIsDelegate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereMaintenanceRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpValidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePickupOtpValidatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereRelatedHelpdeskTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerAcknowledgedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereReturnLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereSpecialInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereSponsorshipToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereSponsorshipTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereTermsAcknowledged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereTrackingToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereTrackingTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withoutTrashed()
 */
	class LoanApplication extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Loan Approval Token Model
 * 
 * Manages secure email-based approval tokens for Bahagian 5 workflow
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property bool $used
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken whereUsedAt($value)
 */
	class LoanApprovalToken extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Item Model
 * 
 * Junction table linking loan applications to specific assets with condition tracking.
 *
 * @see D03-FR-003.2 Asset issuance tracking
 * @see D03-FR-003.3 Asset return processing
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property int $loan_application_id
 * @property int $asset_id
 * @property int $quantity
 * @property float $unit_value
 * @property float $total_value
 * @property AssetCondition|null $condition_before
 * @property AssetCondition|null $condition_after
 * @property array|null $accessories_issued
 * @property array|null $accessories_returned
 * @property string|null $damage_report
 * @property string $equipment_type Type of equipment requested
 * @property string|null $notes Additional notes for equipment request
 * @property string|null $brand_model
 * @property string|null $serial_number
 * @property string|null $other_accessories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\LoanApplication $loanApplication
 * @method static \Database\Factories\LoanItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAccessoriesIssued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAccessoriesReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereBrandModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereDamageReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereEquipmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereOtherAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereUnitValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem whereUpdatedAt($value)
 */
	class LoanItem extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Transaction Model
 * 
 * Complete audit trail for all asset loan transactions.
 *
 * @see D03-FR-010.2 Comprehensive audit logging
 * @see D03-FR-018.3 Asset lifecycle tracking
 * @see D04 §2.2 Model relationships
 * @property int $id
 * @property int $loan_application_id
 * @property int $asset_id
 * @property TransactionType $transaction_type
 * @property int $processed_by
 * @property \Carbon\Carbon $processed_at
 * @property AssetCondition|null $condition_before
 * @property AssetCondition|null $condition_after
 * @property array|null $accessories
 * @property string|null $damage_report
 * @property string|null $notes
 * @property string $created_at Record creation timestamp
 * @property-read \App\Models\Asset $asset
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \App\Models\User $processedBy
 * @method static \Database\Factories\LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAccessories($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereConditionAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereConditionBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDamageReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereTransactionType($value)
 */
	class LoanTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $provider
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed>|null $config
 * @property string|null $capabilities
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $last_synced_at
 * @property string|null $sync_cursor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryEntity> $entities
 * @property-read int|null $entities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryAdapterSync> $syncs
 * @property-read int|null $syncs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereCapabilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereSyncCursor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withoutTrashed()
 */
	class MemoryAdapter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $memory_adapter_id
 * @property string $status
 * @property array<array-key, mixed>|null $payload
 * @property array<array-key, mixed>|null $error
 * @property int $synced_entities
 * @property int $synced_relations
 * @property int $synced_observations
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryAdapter $adapter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereMemoryAdapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedEntities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereSyncedRelations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withoutTrashed()
 */
	class MemoryAdapterSync extends \Eloquent {}
}

namespace App\Models{
/**
 * Represents a node stored inside the cross-extension memory graph.
 *
 * @see D03-FR-020 Memory persistence requirements
 * @property string $id
 * @property string $name
 * @property string $entity_type
 * @property array<array-key, mixed>|null $labels
 * @property string|null $summary
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $source
 * @property string|null $source_identifier
 * @property float|null $confidence
 * @property \Illuminate\Support\Carbon|null $discovered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryRelation> $incomingRelations
 * @property-read int|null $incoming_relations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryObservation> $observations
 * @property-read int|null $observations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryRelation> $outgoingRelations
 * @property-read int|null $outgoing_relations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MemoryEntity> $relatedEntities
 * @property-read int|null $related_entities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereDiscoveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereSourceIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity withoutTrashed()
 */
	class MemoryEntity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $memory_entity_id
 * @property string|null $memory_adapter_id
 * @property string|null $content_hash
 * @property string $content
 * @property array<array-key, mixed>|null $metadata
 * @property float|null $confidence
 * @property \Illuminate\Support\Carbon|null $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryEntity $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereContentHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMemoryAdapterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMemoryEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withoutTrashed()
 */
	class MemoryObservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $from_entity_id
 * @property string $to_entity_id
 * @property string $relation_type
 * @property array<array-key, mixed>|null $metadata
 * @property float|null $confidence
 * @property \Illuminate\Support\Carbon|null $discovered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\MemoryEntity $fromEntity
 * @property-read \App\Models\MemoryEntity $toEntity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereConfidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereDiscoveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereFromEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereRelationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereToEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withoutTrashed()
 */
	class MemoryRelation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $activity_type
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read string $color_class
 * @property-read string $formatted_description
 * @property-read string $icon
 * @property-read \Illuminate\Database\Eloquent\Model|null $subject
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity whereUserId($value)
 */
	class PortalActivity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name_ms
 * @property string $name_en
 * @property string|null $description_ms
 * @property string|null $description_en
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescriptionMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withoutTrashed()
 */
	class Position extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $module
 * @property string $frequency
 * @property \Illuminate\Support\Carbon $schedule_time
 * @property int|null $schedule_day_of_week
 * @property int|null $schedule_day_of_month
 * @property array<array-key, mixed> $recipients
 * @property array<array-key, mixed>|null $filters
 * @property string $format
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_run_at
 * @property \Illuminate\Support\Carbon|null $next_run_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $frequency_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule due()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereNextRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleDayOfMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereScheduleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule whereUpdatedAt($value)
 */
	class ReportSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $search_type
 * @property array<array-key, mixed> $filters
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\SavedSearchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereSearchType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch whereUserId($value)
 */
	class SavedSearch extends \Eloquent {}
}

namespace App\Models{
/**
 * Support Ticket Model
 * 
 * Represents in-app support messages from portal users.
 *
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 * 
 * Requirements:
 * - Requirement 12.4: Support ticket tracking
 * - D09: Database documentation and audit trail
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicketAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read string $ticket_number
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket closed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUserId($value)
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * Support Ticket Attachment Model
 * 
 * Represents file attachments for support tickets.
 *
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 * @property int $id
 * @property int $support_ticket_id
 * @property string $filename
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read string $human_readable_size
 * @property-read \App\Models\SupportTicket $supportTicket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereSupportTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment whereUpdatedAt($value)
 */
	class SupportTicketAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $name_ms
 * @property string $name_en
 * @property string|null $description_ms
 * @property string|null $description_en
 * @property int|null $parent_id
 * @property int $sla_response_hours
 * @property int $sla_resolution_hours
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TicketCategory> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read TicketCategory|null $parent
 * @method static \Database\Factories\TicketCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescriptionMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereNameMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereSlaResolutionHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereSlaResponseHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withoutTrashed()
 */
	class TicketCategory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_backup_codes
 * @property int $two_factor_enabled
 * @property string|null $two_factor_enabled_at
 * @property string|null $remember_token
 * @property string $role
 * @property string|null $staff_id
 * @property int|null $division_id
 * @property int|null $grade_id
 * @property int|null $position_id
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $bio
 * @property string|null $avatar
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $password_changed_at
 * @property bool $require_password_change
 * @property int $has_completed_tour
 * @property array<array-key, mixed>|null $notification_preferences User notification preferences for email alerts
 * @property string|null $anonymized_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $approvedLoanApplications
 * @property-read int|null $approved_loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $assignedHelpdeskTickets
 * @property-read int|null $assigned_helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserConsent> $consents
 * @property-read int|null $consents_count
 * @property-read \App\Models\Division|null $division
 * @property-read int $profile_completeness
 * @property \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $helpdeskComments
 * @property-read int|null $helpdesk_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplications
 * @property-read int|null $loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserNotificationPreference> $notificationPreferences
 * @property-read int|null $notification_preferences_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $portalActivities
 * @property-read int|null $portal_activities_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SavedSearch> $savedSearches
 * @property-read int|null $saved_searches_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User grade41AndAbove()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAnonymizedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHasCompletedTour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRequirePasswordChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorBackupCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Filament\Models\Contracts\FilamentUser, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $consent_type
 * @property string $consent_statement
 * @property string $version
 * @property bool $granted
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $consented_at
 * @property \Illuminate\Support\Carbon|null $revoked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $history_description
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereConsentStatement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereConsentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereConsentedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereGranted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereRevokedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent whereVersion($value)
 */
	class UserConsent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $preference_key
 * @property bool $preference_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference disabled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference enabled()
 * @method static \Database\Factories\UserNotificationPreferenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference wherePreferenceKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference wherePreferenceValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereUserId($value)
 */
	class UserNotificationPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $module
 * @property string|null $description
 * @property array<array-key, mixed> $conditions
 * @property array<array-key, mixed> $actions
 * @property bool $is_active
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule byPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule forModule(string $module)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereActions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereConditions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule whereUpdatedAt($value)
 */
	class WorkflowRule extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

