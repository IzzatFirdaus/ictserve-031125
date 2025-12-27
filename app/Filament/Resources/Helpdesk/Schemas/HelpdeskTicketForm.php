<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Helpdesk Ticket Form Schema
 *
 * Provides admin level management over ticket lifecycle, assignment, and SLA metadata.
 */
class HelpdeskTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.helpdesk.ticket_info'))
                ->schema([
                    TextInput::make('ticket_number')
                        ->label(__('filament.helpdesk.ticket_number'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('subject')
                        ->label(__('filament.helpdesk.subject'))
                        ->required()
                        ->maxLength(255),
                    Select::make('category_id')
                        ->relationship('category', 'name_ms')
                        ->label(__('filament.helpdesk.category'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    ToggleButtons::make('priority')
                        ->label(__('filament.helpdesk.priority'))
                        ->options(self::priorityOptions())
                        ->inline()
                        ->required()
                        ->rule('in:low,normal,high,urgent'),
                    TextInput::make('damage_type')
                        ->label(__('filament.helpdesk.damage_type'))
                        ->maxLength(255),
                    Select::make('asset_id')
                        ->relationship('asset', 'name')
                        ->label(__('filament.helpdesk.related_asset'))
                        ->searchable()
                        ->preload(),
                    ToggleButtons::make('status')
                        ->label(__('filament.helpdesk.status'))
                        ->options(self::statusOptions())
                        ->inline()
                        ->required(),
                ])
                ->columns(2),
            Section::make(__('filament.helpdesk.complainant_info'))
                ->description(__('filament.helpdesk.complainant_info_desc'))
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label(__('filament.helpdesk.registered_user'))
                        ->searchable()
                        ->preload()
                        ->helperText(__('filament.helpdesk.registered_user_help'))
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $state ? self::clearGuestFields($set) : null),

                    // Guest fields - shown when user_id is null
                    TextInput::make('guest_name')
                        ->label(__('filament.helpdesk.guest_name'))
                        ->maxLength(255)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_email')
                        ->label(__('filament.helpdesk.guest_email'))
                        ->email()
                        ->maxLength(255)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_phone')
                        ->label(__('filament.helpdesk.guest_phone'))
                        ->tel()
                        ->maxLength(30)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    TextInput::make('guest_staff_id')
                        ->label(__('filament.helpdesk.guest_staff_id'))
                        ->maxLength(50)
                        ->visible(fn (callable $get) => ! $get('user_id'))
                        ->required(fn (callable $get) => ! $get('user_id')),
                    Select::make('division_id')
                        ->relationship('division', 'name_ms')
                        ->label(__('filament.helpdesk.division'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('job_grade')
                        ->label(__('filament.helpdesk.job_grade'))
                        ->options(self::jobGradeOptions())
                        ->searchable()
                        ->required(),
                    Checkbox::make('declaration_accepted')
                        ->label(__('filament.helpdesk.declaration'))
                        ->accepted()
                        ->validationMessages([
                            'accepted' => __('filament.helpdesk.declaration_error'),
                        ])
                        ->columnSpanFull()
                        ->required(),

                    Textarea::make('description')
                        ->label(__('filament.helpdesk.description'))
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make(__('filament.helpdesk.assignment_sla'))
                ->schema([
                    Select::make('assigned_to_division')
                        ->relationship('assignedDivision', 'name_ms')
                        ->label(__('filament.helpdesk.assigned_division'))
                        ->searchable()
                        ->preload(),
                    TextInput::make('assigned_to_agency')
                        ->label(__('filament.helpdesk.external_agency'))
                        ->maxLength(255),
                    Select::make('assigned_to_user')
                        ->relationship('assignedUser', 'name')
                        ->label(__('filament.helpdesk.assigned_officer'))
                        ->searchable()
                        ->preload(),
                    DateTimePicker::make('sla_response_due_at')
                        ->label(__('filament.helpdesk.sla_response'))
                        ->seconds(false),
                    DateTimePicker::make('sla_resolution_due_at')
                        ->label(__('filament.helpdesk.sla_resolution'))
                        ->seconds(false),
                    DateTimePicker::make('responded_at')
                        ->label(__('filament.helpdesk.response_date'))
                        ->seconds(false),
                    DateTimePicker::make('resolved_at')
                        ->label(__('filament.helpdesk.resolved_date'))
                        ->seconds(false),
                    DateTimePicker::make('closed_at')
                        ->label(__('filament.helpdesk.closed_date'))
                        ->seconds(false),
                ])
                ->columns(3),
            Section::make(__('filament.helpdesk.notes'))
                ->schema([
                    Textarea::make('admin_notes')
                        ->label(__('filament.helpdesk.admin_notes'))
                        ->rows(3),
                    Textarea::make('internal_notes')
                        ->label(__('filament.helpdesk.internal_notes'))
                        ->rows(3),
                    Textarea::make('resolution_notes')
                        ->label(__('filament.helpdesk.resolution_notes'))
                        ->rows(3),
                ])
                ->columns(1),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function statusOptions(): array
    {
        return [
            'open' => __('filament.helpdesk.status_open'),
            'assigned' => __('filament.helpdesk.status_assigned'),
            'in_progress' => __('filament.helpdesk.status_in_progress'),
            'pending_user' => __('filament.helpdesk.status_pending_user'),
            'resolved' => __('filament.helpdesk.status_resolved'),
            'closed' => __('filament.helpdesk.status_closed'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function priorityOptions(): array
    {
        return [
            'low' => __('filament.helpdesk.priority_low'),
            'normal' => __('filament.helpdesk.priority_normal'),
            'high' => __('filament.helpdesk.priority_high'),
            'urgent' => __('filament.helpdesk.priority_urgent'),
        ];
    }

    /**
     * Clear guest fields when authenticated user is selected
     */
    private static function clearGuestFields(callable $set): void
    {
        $set('guest_name', null);
        $set('guest_email', null);
        $set('guest_phone', null);
        $set('guest_staff_id', null);
    }

    /**
     * @return array<int|string, string>
     */
    private static function jobGradeOptions(): array
    {
        return [
            '11' => __('filament.grades.grade_11'),
            '17' => __('filament.grades.grade_17'),
            '19' => __('filament.grades.grade_19'),
            '22' => __('filament.grades.grade_22'),
            '26' => __('filament.grades.grade_26'),
            '27' => __('filament.grades.grade_27'),
            '29' => __('filament.grades.grade_29'),
            '32' => __('filament.grades.grade_32'),
            '36' => __('filament.grades.grade_36'),
            '38' => __('filament.grades.grade_38'),
            '41' => __('filament.grades.grade_41'),
            '42' => __('filament.grades.grade_42'),
            '44' => __('filament.grades.grade_44'),
            '45' => __('filament.grades.grade_45'),
            '48' => __('filament.grades.grade_48'),
            '52' => __('filament.grades.grade_52'),
            '54' => __('filament.grades.grade_54'),
            '56' => __('filament.grades.grade_56'),
            'JUSA_A' => __('filament.grades.jusa_a'),
            'JUSA_B' => __('filament.grades.jusa_b'),
            'JUSA_C' => __('filament.grades.jusa_c'),
        ];
    }
}
