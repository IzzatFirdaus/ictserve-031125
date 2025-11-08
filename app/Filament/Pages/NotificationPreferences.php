<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

/**
 * Notification Preferences
 *
 * Allows admin users to configure their notification preferences including
 * delivery methods, frequency settings, and notification types.
 *
 * Requirements: 10.4
 *
 * @see D03-FR-008.4 Notification preferences
 * @see D04 §8.1 Notification system
 */
class NotificationPreferences extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.notification-preferences';

    protected static ?string $navigationLabel = 'Notification Preferences';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'notification-preferences';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Notification Preferences');
    }

    public function mount(): void
    {
        $user = auth()->user();
        $preferences = $user->notification_preferences ?? [];

        $this->form->fill([
            'email_notifications' => $preferences['email_notifications'] ?? true,
            'in_app_notifications' => $preferences['in_app_notifications'] ?? true,
            'sms_notifications' => $preferences['sms_notifications'] ?? false,
            'desktop_notifications' => $preferences['desktop_notifications'] ?? true,

            // Notification Types
            'helpdesk_notifications' => $preferences['helpdesk_notifications'] ?? [
                'ticket_assigned' => true,
                'ticket_status_changed' => true,
                'sla_breach' => true,
                'overdue_tickets' => true,
            ],
            'loan_notifications' => $preferences['loan_notifications'] ?? [
                'application_submitted' => true,
                'application_approved' => true,
                'application_rejected' => true,
                'asset_overdue' => true,
                'return_reminder' => true,
            ],
            'security_notifications' => $preferences['security_notifications'] ?? [
                'security_incidents' => true,
                'failed_logins' => true,
                'role_changes' => true,
                'config_changes' => true,
            ],
            'system_notifications' => $preferences['system_notifications'] ?? [
                'maintenance_alerts' => true,
                'performance_alerts' => false,
                'backup_status' => false,
                'update_notifications' => true,
            ],

            // Frequency Settings
            'digest_frequency' => $preferences['digest_frequency'] ?? 'daily',
            'quiet_hours_enabled' => $preferences['quiet_hours_enabled'] ?? false,
            'quiet_hours_start' => $preferences['quiet_hours_start'] ?? '22:00',
            'quiet_hours_end' => $preferences['quiet_hours_end'] ?? '08:00',
            'weekend_notifications' => $preferences['weekend_notifications'] ?? false,

            // Priority Settings
            'urgent_only_mode' => $preferences['urgent_only_mode'] ?? false,
            'priority_threshold' => $preferences['priority_threshold'] ?? 'medium',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Delivery Methods')
                    ->description('Choose how you want to receive notifications')
                    ->schema([
                        Forms\Components\Toggle::make('email_notifications')
                            ->label('Email Notifications')
                            ->helperText('Receive notifications via email')
                            ->default(true),

                        Forms\Components\Toggle::make('in_app_notifications')
                            ->label('In-App Notifications')
                            ->helperText('Show notifications in the admin panel')
                            ->default(true),

                        Forms\Components\Toggle::make('sms_notifications')
                            ->label('SMS Notifications')
                            ->helperText('Receive critical notifications via SMS')
                            ->default(false),

                        Forms\Components\Toggle::make('desktop_notifications')
                            ->label('Desktop Notifications')
                            ->helperText('Show browser desktop notifications')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Helpdesk Notifications')
                    ->description('Configure helpdesk-related notifications')
                    ->schema([
                        Forms\Components\CheckboxList::make('helpdesk_notifications')
                            ->label('Notification Types')
                            ->options([
                                'ticket_assigned' => 'Ticket Assigned to Me',
                                'ticket_status_changed' => 'Ticket Status Changes',
                                'sla_breach' => 'SLA Breach Alerts',
                                'overdue_tickets' => 'Overdue Ticket Reminders',
                                'new_comments' => 'New Comments on My Tickets',
                                'escalation_alerts' => 'Ticket Escalation Alerts',
                            ])
                            ->columns(2)
                            ->default(['ticket_assigned', 'ticket_status_changed', 'sla_breach', 'overdue_tickets']),
                    ]),

                Forms\Components\Section::make('Asset Loan Notifications')
                    ->description('Configure asset loan-related notifications')
                    ->schema([
                        Forms\Components\CheckboxList::make('loan_notifications')
                            ->label('Notification Types')
                            ->options([
                                'application_submitted' => 'New Loan Applications',
                                'application_approved' => 'Application Approvals',
                                'application_rejected' => 'Application Rejections',
                                'asset_overdue' => 'Overdue Asset Alerts',
                                'return_reminder' => 'Return Reminders',
                                'damage_reports' => 'Asset Damage Reports',
                            ])
                            ->columns(2)
                            ->default(['application_submitted', 'application_approved', 'asset_overdue']),
                    ]),

                Forms\Components\Section::make('Security Notifications')
                    ->description('Configure security-related notifications')
                    ->schema([
                        Forms\Components\CheckboxList::make('security_notifications')
                            ->label('Notification Types')
                            ->options([
                                'security_incidents' => 'Security Incidents',
                                'failed_logins' => 'Failed Login Attempts',
                                'role_changes' => 'User Role Changes',
                                'config_changes' => 'System Configuration Changes',
                                'suspicious_activity' => 'Suspicious Activity Alerts',
                                'audit_alerts' => 'Audit Trail Alerts',
                            ])
                            ->columns(2)
                            ->default(['security_incidents', 'failed_logins', 'role_changes']),
                    ]),

                Forms\Components\Section::make('System Notifications')
                    ->description('Configure system-related notifications')
                    ->schema([
                        Forms\Components\CheckboxList::make('system_notifications')
                            ->label('Notification Types')
                            ->options([
                                'maintenance_alerts' => 'Maintenance Alerts',
                                'performance_alerts' => 'Performance Alerts',
                                'backup_status' => 'Backup Status Updates',
                                'update_notifications' => 'System Update Notifications',
                                'integration_alerts' => 'Integration Failure Alerts',
                                'queue_alerts' => 'Queue Processing Alerts',
                            ])
                            ->columns(2)
                            ->default(['maintenance_alerts', 'update_notifications']),
                    ]),

                Forms\Components\Section::make('Frequency & Timing')
                    ->description('Configure when and how often you receive notifications')
                    ->schema([
                        Forms\Components\Select::make('digest_frequency')
                            ->label('Digest Frequency')
                            ->helperText('How often to receive notification summaries')
                            ->options([
                                'immediate' => 'Immediate (Real-time)',
                                'hourly' => 'Hourly Digest',
                                'daily' => 'Daily Digest',
                                'weekly' => 'Weekly Digest',
                            ])
                            ->default('daily'),

                        Forms\Components\Toggle::make('quiet_hours_enabled')
                            ->label('Enable Quiet Hours')
                            ->helperText('Suppress non-urgent notifications during specified hours')
                            ->reactive(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('quiet_hours_start')
                                    ->label('Quiet Hours Start')
                                    ->default('22:00')
                                    ->visible(fn ($get) => $get('quiet_hours_enabled')),

                                Forms\Components\TimePicker::make('quiet_hours_end')
                                    ->label('Quiet Hours End')
                                    ->default('08:00')
                                    ->visible(fn ($get) => $get('quiet_hours_enabled')),
                            ]),

                        Forms\Components\Toggle::make('weekend_notifications')
                            ->label('Weekend Notifications')
                            ->helperText('Receive notifications during weekends')
                            ->default(false),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Priority Settings')
                    ->description('Configure notification priority filtering')
                    ->schema([
                        Forms\Components\Toggle::make('urgent_only_mode')
                            ->label('Urgent Only Mode')
                            ->helperText('Only receive urgent and critical notifications')
                            ->default(false),

                        Forms\Components\Select::make('priority_threshold')
                            ->label('Minimum Priority Level')
                            ->helperText('Only receive notifications at or above this priority level')
                            ->options([
                                'low' => 'Low and above',
                                'medium' => 'Medium and above',
                                'high' => 'High and above',
                                'urgent' => 'Urgent only',
                            ])
                            ->default('medium')
                            ->visible(fn ($get) => ! $get('urgent_only_mode')),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Preferences')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),

            Action::make('reset_defaults')
                ->label('Reset to Defaults')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset Notification Preferences')
                ->modalDescription('Are you sure you want to reset all notification preferences to their default values?')
                ->action('resetToDefaults'),

            Action::make('test_notifications')
                ->label('Test Notifications')
                ->icon('heroicon-o-bell')
                ->color('info')
                ->action('sendTestNotifications'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'notification_preferences' => $data,
        ]);

        // Clear user's notification cache
        Cache::forget("user_notification_preferences_{$user->id}");

        \Filament\Notifications\Notification::make()
            ->title('Notification preferences saved successfully.')
            ->success()
            ->send();
    }

    public function resetToDefaults(): void
    {
        $user = auth()->user();

        $defaultPreferences = [
            'email_notifications' => true,
            'in_app_notifications' => true,
            'sms_notifications' => false,
            'desktop_notifications' => true,
            'helpdesk_notifications' => [
                'ticket_assigned' => true,
                'ticket_status_changed' => true,
                'sla_breach' => true,
                'overdue_tickets' => true,
            ],
            'loan_notifications' => [
                'application_submitted' => true,
                'application_approved' => true,
                'application_rejected' => true,
                'asset_overdue' => true,
                'return_reminder' => true,
            ],
            'security_notifications' => [
                'security_incidents' => true,
                'failed_logins' => true,
                'role_changes' => true,
                'config_changes' => true,
            ],
            'system_notifications' => [
                'maintenance_alerts' => true,
                'performance_alerts' => false,
                'backup_status' => false,
                'update_notifications' => true,
            ],
            'digest_frequency' => 'daily',
            'quiet_hours_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
            'weekend_notifications' => false,
            'urgent_only_mode' => false,
            'priority_threshold' => 'medium',
        ];

        $user->update([
            'notification_preferences' => $defaultPreferences,
        ]);

        $this->form->fill($defaultPreferences);

        // Clear user's notification cache
        Cache::forget("user_notification_preferences_{$user->id}");

        \Filament\Notifications\Notification::make()
            ->title('Notification preferences reset to defaults.')
            ->success()
            ->send();
    }

    public function sendTestNotifications(): void
    {
        $user = auth()->user();

        // Send test notifications based on enabled delivery methods
        $preferences = $user->notification_preferences ?? [];

        if ($preferences['email_notifications'] ?? true) {
            // Send test email notification
            $user->notify(new \App\Notifications\TestNotification('email'));
        }

        if ($preferences['in_app_notifications'] ?? true) {
            // Send test in-app notification
            $user->notify(new \App\Notifications\TestNotification('in_app'));
        }

        \Filament\Notifications\Notification::make()
            ->title('Test notifications sent successfully. Please check your configured delivery methods.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'preferences' => $this->data,
        ];
    }
}
