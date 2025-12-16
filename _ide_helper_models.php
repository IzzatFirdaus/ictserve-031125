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
 * Activity Model for Spatie Activity Log
 * 
 * Represents user activity logs for operational dashboards and reports.
 * Part of the Dual Audit System complementing owen-it/laravel-auditing.
 *
 * @see D09 §4.7 Activity logging requirements
 * @see Requirements 19.2, 19.4
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $event
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property array<string, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|null $subject
 * @property-read Model|null $causer
 * @property-read string $causer_name
 * @property-read string $subject_name
 * @property-read \Illuminate\Database\Eloquent\Model $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity dateRange(\Illuminate\Support\Carbon $startDate, \Illuminate\Support\Carbon $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity inLog(string ...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 */
	class Activity extends \Eloquent {}
}

namespace App\Models{
/**
 * ApiTokenUsageLog Model - v3.5.0 True Hybrid Architecture
 * 
 * Tracks API token usage for security auditing and monitoring.
 * Records all API requests made with Sanctum tokens.
 *
 * @see D03 Software Requirements Specification - Requirement 37.5
 * @see D04 Software Design Document - API Token Service
 * @see D09 Database Documentation - api_token_usage_logs table
 * @property int $id
 * @property int $personal_access_token_id
 * @property int $user_id
 * @property string $action
 * @property string $endpoint
 * @property string $ip_hash
 * @property string|null $user_agent
 * @property int|null $response_status
 * @property \Carbon\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Laravel\Sanctum\PersonalAccessToken|null $personalAccessToken
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiTokenUsageLog query()
 */
	class ApiTokenUsageLog extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * ApprovalDelegation Model
 * 
 * Manages temporary delegation of approval authority from one approver to another.
 * Supports Grade 41+ approvers delegating their approval responsibilities during
 * leave periods or temporary unavailability.
 *
 * @property int $id
 * @property int $original_approver_id
 * @property int $delegated_approver_id
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property string $reason
 * @property bool $is_active
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $delegatedApprover
 * @property-read \App\Models\User|null $originalApprover
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalDelegation upcoming()
 */
	class ApprovalDelegation extends \Eloquent {}
}

namespace App\Models{
/**
 * Model ApprovalEmailToken untuk token kelulusan e-mel
 * 
 * Menyokong aliran kerja kelulusan auto-reply melalui e-mel
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property int $auto_reply_draft_id ID draf auto-reply
 * @property string $token Token kelulusan selamat
 * @property string $action Tindakan: approve atau reject
 * @property \Carbon\Carbon $expires_at Masa tamat tempoh
 * @property bool $used Status penggunaan token
 * @property \Carbon\Carbon|null $used_at Masa token digunakan
 * @property string|null $used_by_ip IP address pengguna
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\AutoReplyDraft|null $autoReplyDraft
 * @property-read string $action_label
 * @property-read bool $is_expired
 * @property-read bool $is_valid
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken byAction(string $action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken byToken(string $token)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken expired()
 * @method static \Database\Factories\ApprovalEmailTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken unused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken used()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken valid()
 */
	class ApprovalEmailToken extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\AssetCategory|null $category
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\AssetCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCategory withoutTrashed()
 */
	class AssetCategory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\User|null $performedByUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetMaintenance query()
 */
	class AssetMaintenance extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \App\Models\User|null $processedByUser
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransaction query()
 */
	class AssetTransaction extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\User|null $fromUser
 * @property-read \App\Models\User|null $initiator
 * @property-read \App\Models\User|null $toUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetTransfer query()
 */
	class AssetTransfer extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Model $user
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
 */
	class Audit extends \Eloquent {}
}

namespace App\Models{
/**
 * Model AutoReplyDraft untuk sistem AI Ollama
 * 
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply draft management dengan approval workflow
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $replyable_type
 * @property int $replyable_id
 * @property string $draft_content
 * @property int|null $template_id
 * @property string $status
 * @property int $generated_by
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $replyable
 * @property-read \App\Models\AutoReplyTemplate|null $template
 * @property-read \App\Models\User $generator
 * @property-read \App\Models\User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $preview
 * @property-read string $status_color
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft approved()
 * @method static \Database\Factories\AutoReplyDraftFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft pendingReview()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft withStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyDraft withoutTrashed()
 */
	class AutoReplyDraft extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Model AutoReplyTemplate untuk sistem AI Ollama
 * 
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply template management
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $name
 * @property string $template_content
 * @property array|null $variables
 * @property string $status
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\AutoReplyDraft> $drafts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read int|null $drafts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate draft()
 * @method static \Database\Factories\AutoReplyTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AutoReplyTemplate withoutTrashed()
 */
	class AutoReplyTemplate extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockConversation query()
 */
	class BedrockConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $creator
 * @method static \Database\Factories\BedrockModelConfigFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockModelConfig withoutTrashed()
 */
	class BedrockModelConfig extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\BedrockUsageLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BedrockUsageLog query()
 */
	class BedrockUsageLog extends \Eloquent {}
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
 */
	class BlockedIp extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ConversationContextFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationContext query()
 */
	class ConversationContext extends \Eloquent {}
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrossModuleIntegration withoutTrashed()
 */
	class CrossModuleIntegration extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Model DataLineage untuk penjejakan lineage data AI
 * 
 * Merekod transformasi data untuk pematuhan PDPA 2010
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $lineage_id ID unik untuk lineage tracking
 * @property string $source_type Jenis sumber data
 * @property int $source_id ID sumber
 * @property string $transformation_type Jenis transformasi
 * @property array $transformation_metadata Metadata transformasi
 * @property string $destination_type Jenis destinasi
 * @property int|null $destination_id ID destinasi
 * @property \Carbon\Carbon $processed_at Masa pemprosesan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $transformation_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage byDestination(string $destinationType, ?int $destinationId = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage bySource(string $sourceType, int $sourceId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage bySourceType(string $sourceType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage byTransformationType(string $transformationType)
 * @method static \Database\Factories\DataLineageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataLineage query()
 */
	class DataLineage extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withoutTrashed()
 */
	class Division extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Model Document untuk sistem AI Ollama
 * 
 * Per Requirements 2.1, 2.2, 4.1: Document management dengan True Hybrid Architecture
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $filename
 * @property array|null $metadata
 * @property int|null $uploaded_by
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $uploader
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\DocumentChunk> $chunks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read int|null $chunks_count
 * @property-read string|null $file_size
 * @property-read string|null $file_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document completed()
 * @method static \Database\Factories\DocumentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document withStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document withoutTrashed()
 */
	class Document extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Model DocumentChunk untuk sistem AI Ollama
 * 
 * Per Requirements 2.1, 2.2: Document chunking untuk vector embeddings
 * Selaras dengan D09 Database Documentation v3.6.0
 *
 * @property int $id
 * @property int $document_id
 * @property string $chunk_text
 * @property array $embedding
 * @property string|null $source
 * @property int $chunk_index
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Document $document
 * @property-read string $preview
 * @property-read int $text_length
 * @method static \Database\Factories\DocumentChunkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk withEmbedding()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentChunk withIndex(int $index)
 */
	class DocumentChunk extends \Eloquent {}
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailLog withPriority(string $priority)
 */
	class EmailLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate forCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate forLocale(string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 */
	class EmailTemplate extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedJob query()
 */
	class FailedJob extends \Eloquent {}
}

namespace App\Models{
/**
 * Model FAQ untuk sistem AI Ollama
 * 
 * Per Requirements 1.1, 1.5, 4.1: FAQ management dengan True Hybrid Architecture
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property array|null $tags
 * @property float|null $match_score
 * @property int|null $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\FaqFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq search(string $searchQuery)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withMinScore(float $minScore = 0.3)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq withoutTrashed()
 */
	class Faq extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 */
	class Grade extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Model GuestConversation untuk sejarah perbualan tetamu
 * 
 * Menyokong Account Linking untuk True Hybrid Architecture
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $session_id ID sesi tetamu
 * @property string|null $email E-mel tetamu (opsyen)
 * @property array $conversation_history Sejarah perbualan
 * @property int|null $claimed_by_user_id Pengguna yang menuntut
 * @property \Carbon\Carbon|null $claimed_at Masa dituntut
 * @property \Carbon\Carbon $expires_at Masa tamat tempoh
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $claimedByUser
 * @property-read bool $is_active
 * @property-read bool $is_claimed
 * @property-read int $message_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation byEmail(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation bySession(string $sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation claimed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation expired()
 * @method static \Database\Factories\GuestConversationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation unclaimed()
 */
	class GuestConversation extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket|null $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withoutTrashed()
 */
	class HelpdeskAttachment extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\HelpdeskTicket|null $helpdeskTicket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment query()
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
 * @property-read \App\Models\TicketCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CrossModuleIntegration> $crossModuleIntegrations
 * @property-read int|null $cross_module_integrations_count
 * @property-read \App\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \App\Models\HelpdeskComment|null $latestComment
 * @property-read \App\Models\Asset|null $relatedAsset
 * @property-write mixed $priority
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket bySLA(string $slaStatus)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket byStatusToken(string $tokenHash)
 * @method static \Database\Factories\HelpdeskTicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket optimizedCount()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket optimizedPagination(int $perPage = 25)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withCommonRelations()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withoutTrashed()
 */
	class HelpdeskTicket extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Model $commentable
 * @property-read string $formatted_comment
 * @property-read InternalComment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InternalComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\InternalCommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InternalComment topLevel()
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Division|null $division
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication byApprovalToken(string $tokenHash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication byStatusToken(string $tokenHash)
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withoutTrashed()
 */
	class LoanApplication extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * LoanApproval Model - v3.5.0 True Hybrid Architecture
 * 
 * Records approval decisions for loan applications with audit trail.
 * Supports email-based approval workflow with token validation.
 *
 * @see D03 Software Requirements Specification - Requirement 4.3
 * @see D04 Software Design Document - Approval Service
 * @see D09 Database Documentation - loan_approvals table
 * @property int $id
 * @property int $loan_application_id
 * @property string $approver_email
 * @property string $approver_grade
 * @property string $decision (APPROVED, REJECTED)
 * @property string|null $remarks
 * @property \Carbon\Carbon $decision_at
 * @property string $decision_ip_hash
 * @property string $token_hash
 * @property array|null $metadata
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval query()
 */
	class LoanApproval extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * Loan Approval Token Model
 * 
 * Manages secure email-based approval tokens for Bahagian 5 workflow
 *
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApprovalToken query()
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
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @method static \Database\Factories\LoanItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanItem query()
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \App\Models\User|null $processedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionAccessory> $transactionAccessories
 * @property-read int|null $transaction_accessories_count
 * @method static \Database\Factories\LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction query()
 */
	class LoanTransaction extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * LoanTransactionAccessory Model - v3.5.0 True Hybrid Architecture
 * 
 * Tracks accessories included with asset loans during check-out and check-in.
 * Supports discrepancy detection for missing or damaged accessories.
 *
 * @see D03 Software Requirements Specification - Requirement 26.6
 * @see D04 Software Design Document - Accessory Tracking Service
 * @see D09 Database Documentation - loan_transaction_accessories table
 * @property int $id
 * @property int $loan_transaction_id
 * @property string $accessory_type (POWER_ADAPTER, BAG, MOUSE, USB_CABLE, HDMI_VGA_CABLE, REMOTE, OTHERS)
 * @property string|null $accessory_name (for OTHERS type)
 * @property bool $present_at_checkout
 * @property bool|null $present_at_checkin
 * @property string|null $condition_notes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanTransaction|null $loanTransaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionAccessory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionAccessory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionAccessory query()
 */
	class LoanTransactionAccessory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryEntity> $entities
 * @property-read int|null $entities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryAdapterSync> $syncs
 * @property-read int|null $syncs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapter withoutTrashed()
 */
	class MemoryAdapter extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\MemoryAdapter|null $adapter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryAdapterSync withoutTrashed()
 */
	class MemoryAdapterSync extends \Eloquent {}
}

namespace App\Models{
/**
 * Memory Entity Model
 *
 * @property string $id
 * @property string $name
 * @property string $entity_type
 * @property array<int, string>|null $labels
 * @property string|null $summary
 * @property array<string, mixed>|null $metadata
 * @property string|null $source
 * @property string|null $source_identifier
 * @property float|null $confidence
 * @property \Carbon\Carbon|null $discovered_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryObservation> $observations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryRelation> $relationsFrom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MemoryRelation> $relationsTo
 * @property-read int|null $observations_count
 * @property-read int|null $relations_from_count
 * @property-read int|null $relations_to_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryEntity withoutTrashed()
 */
	class MemoryEntity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\MemoryAdapter|null $adapter
 * @property-read \App\Models\MemoryEntity|null $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryObservation withoutTrashed()
 */
	class MemoryObservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\MemoryEntity|null $from
 * @property-read \App\Models\MemoryEntity|null $to
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemoryRelation withoutTrashed()
 */
	class MemoryRelation extends \Eloquent {}
}

namespace App\Models{
/**
 * Model MessageLog untuk jejak audit operasi AI
 * 
 * Menyokong True Hybrid Architecture dengan nullable user_id FK
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $request_id X-Request-ID untuk kebolehkesanan
 * @property string $operation_type Jenis operasi AI
 * @property int|null $user_id Pengguna (nullable untuk tetamu)
 * @property string $sanitized_input Input yang disanitasi
 * @property string|null $response_summary Ringkasan respons
 * @property array|null $metadata Model, token, masa pemprosesan
 * @property string $hash SHA-256 hash untuk immutability
 * @property string|null $previous_hash Chain of custody
 * @property \Carbon\Carbon $processed_at Masa pemprosesan
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $user_display_name
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog byDateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog byOperationType(string $operationType)
 * @method static \Database\Factories\MessageLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MessageLog query()
 */
	class MessageLog extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @method static \Database\Factories\ModelPerformanceMetricFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelPerformanceMetric query()
 */
	class ModelPerformanceMetric extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read string $color_class
 * @property-read string $formatted_description
 * @property-read string $icon
 * @property-read \Illuminate\Database\Eloquent\Model $subject
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalActivity query()
 */
	class PortalActivity extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withoutTrashed()
 */
	class Position extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read string $frequency_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule due()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReportSchedule query()
 */
	class ReportSchedule extends \Eloquent {}
}

namespace App\Models{
/**
 * SavedSearch Model
 * 
 * Stores user search history and saved searches for cross-module search.
 * Uses existing table schema with search_type for categorization.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $search_type
 * @property array<string, mixed>|null $filters
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @property-read \Carbon\Carbon|null $last_used_at
 * @property-read string|null $query
 * @property-read int $result_count
 * @method static \Database\Factories\SavedSearchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch history()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SavedSearch saved()
 */
	class SavedSearch extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session query()
 */
	class Session extends \Eloquent {}
}

namespace App\Models{
/**
 * SSO Audit Log Model
 * 
 * Tracks all Google SSO authentication attempts for security monitoring,
 * compliance auditing, and administrative oversight.
 * 
 * Supports Requirements 4.1, 4.2 - Enhanced Security and Audit Logging
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property string|null $google_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property bool $success
 * @property string|null $error_type
 * @property string|null $error_message
 * @property \Carbon\Carbon $attempted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog betweenDates(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog domainErrors()
 * @method static \Database\Factories\SsoAuditLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog forEmail(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog fromIp(string $ipAddress)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog networkErrors()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog oAuthErrors()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog successful()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SsoAuditLog withErrorType(string $errorType)
 */
	class SsoAuditLog extends \Eloquent {}
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
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket closed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
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
 * @property-read \App\Models\SupportTicket|null $supportTicket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicketAttachment query()
 */
	class SupportTicketAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TicketCategory> $children
 * @property-read int|null $children_count
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $helpdeskTickets
 * @property-read int|null $helpdesk_tickets_count
 * @property-read TicketCategory|null $parent
 * @method static \Database\Factories\TicketCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory withoutTrashed()
 */
	class TicketCategory extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $approvedLoanApplications
 * @property-read int|null $approved_loan_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $assignedHelpdeskTickets
 * @property-read int|null $assigned_helpdesk_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $assignedTickets
 * @property-read int|null $assigned_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserConsent> $consents
 * @property-read int|null $consents_count
 * @property-read \App\Models\Division|null $division
 * @property string $locale
 * @property-read string $name
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SsoAuditLog> $ssoAuditLogs
 * @property-read int|null $sso_audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User grade41AndAbove()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable, \Filament\Models\Contracts\FilamentUser, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property-read string $history_description
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConsent query()
 */
	class UserConsent extends \Eloquent {}
}

namespace App\Models{
/**
 * User Notification Preferences Model
 *
 * @property int $id
 * @property int $user_id
 * @property bool $email_digest_enabled
 * @property string $email_digest_frequency
 * @property \Carbon\Carbon $email_digest_time
 * @property bool $quiet_hours_enabled
 * @property \Carbon\Carbon|null $quiet_hours_start
 * @property \Carbon\Carbon|null $quiet_hours_end
 * @property bool $browser_notifications_enabled
 * @property bool $sound_enabled
 * @property bool $group_notifications
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference query()
 */
	class UserNotificationPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\WebSearchLogFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebSearchLog query()
 */
	class WebSearchLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule byPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule forModule(string $module)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkflowRule query()
 */
	class WorkflowRule extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

