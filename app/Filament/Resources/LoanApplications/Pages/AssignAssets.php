<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Pages;

use App\Enums\LoanStatus;
use App\Filament\Resources\LoanApplications\LoanApplicationResource;
use App\Mail\Loan\AssetReadyForCollection;
use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/** @property Schema $form */
class AssignAssets extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = LoanApplicationResource::class;

    protected string $view = 'filament.resources.loan-applications.pages.assign-assets';

    /** @var array<string, mixed> */
    public array $data = [];

    public LoanApplication $record;

    public function mount(int|string $record): void
    {
        $this->record = LoanApplication::findOrFail($record);

        /** @var Collection<int, LoanItem> $loanItems */
        $loanItems = $this->record->loanItems;

        $this->form->fill([
            'actual_issue_date' => now()->format('Y-m-d'),
            'assets' => $loanItems->map(
                /** @return array<string, mixed> */
                static function (LoanItem $item): array {
                    $categoryId = $item->getAttribute('asset_category_id') ?? $item->getAttribute('equipment_type');
                    $quantity = $item->getAttribute('quantity');
                    $requestedQuantity = is_numeric($quantity) ? (int) $quantity : 0;

                    return [
                        'category_id' => $categoryId,
                        'category_name' => $item->getAttribute('category_name') ?? 'N/A',
                        'requested_quantity' => $requestedQuantity,
                        'asset_id' => null,
                        'asset_serial_number' => null,
                        'asset_condition' => 'good',
                    ];
                },
            )->toArray(),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                DatePicker::make('actual_issue_date')
                    ->label(__('loan.filament.actual_issue_date'))
                    ->required()
                    ->default(now())
                    ->maxDate(now()->addDays(7)),
                Repeater::make('assets')
                    ->label(__('loan.filament.assets_to_assign'))
                    ->schema([
                        TextInput::make('category_name')
                            ->label(__('loan.filament.category'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('requested_quantity')
                            ->label(__('loan.filament.requested_quantity'))
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('asset_id')
                            ->label(__('loan.filament.asset'))
                            ->options(function (callable $get) {
                                $categoryId = $get('category_id');
                                if (! $categoryId) {
                                    return [];
                                }

                                return Asset::where('category_id', $categoryId)
                                    ->where('status', 'available')
                                    ->whereNotNull('serial_number')
                                    ->pluck('serial_number', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $asset = Asset::query()->find($state);
                                    if ($asset instanceof Asset) {
                                        $set('asset_serial_number', $asset->getAttribute('asset_serial_number') ?? $asset->serial_number);
                                        $assetCondition = $asset->condition instanceof \App\Enums\AssetCondition
                                            ? $asset->condition->value
                                            : 'good';
                                        $set('asset_condition', $assetCondition);
                                    }
                                }
                            }),
                        TextInput::make('asset_serial_number')
                            ->label(__('loan.filament.serial_number'))
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('asset_condition')
                            ->label(__('loan.filament.asset_condition'))
                            ->options([
                                'excellent' => __('loan.filament.condition.excellent'),
                                'good' => __('loan.filament.condition.good'),
                                'fair' => __('loan.filament.condition.fair'),
                            ])
                            ->required()
                            ->default('good'),
                    ])
                    ->columns(2)
                    ->reorderable(false)
                    ->deletable(false)
                    ->addable(false)
                    ->defaultItems(0),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('assign')
                ->label(__('loan.filament.confirm_assignment'))
                ->color('success')
                ->requiresConfirmation()
                ->action('assignAssets'),
        ];
    }

    public function assignAssets(): void
    {
        $data = $this->form->getState();

        if (! isset($data['assets']) || ! is_array($data['assets'])) {
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($data['assets'] as $assetData) {
                /**
                 * @var array{
                 *     asset_id:int|null,
                 *     asset_serial_number?:string|null,
                 *     asset_condition?:string|null
                 * } $assetData
                 */
                if (! isset($assetData['asset_id'])) {
                    continue;
                }

                // Create asset transaction record
                AssetTransaction::create([
                    'asset_id' => $assetData['asset_id'],
                    'loan_application_id' => $this->record->id,
                    'transaction_type' => 'loan_issue',
                    'asset_serial_number' => $assetData['asset_serial_number'] ?? null,
                    'asset_condition' => $assetData['asset_condition'] ?? 'good',
                    'actual_issue_date' => $data['actual_issue_date'],
                    'issued_by_staff_id' => auth()->id(),
                    'transaction_date' => now(),
                ]);

                // Update asset status
                $asset = Asset::find($assetData['asset_id']);
                if ($asset) {
                    $asset->update(['status' => 'on_loan']);
                }
            }

            // Update loan application status
            $this->record->update([
                'status' => LoanStatus::ISSUED,
                'actual_issue_date' => $data['actual_issue_date'],
            ]);

            // Send notification email
            Mail::to($this->record->applicant_email)
                ->send(new AssetReadyForCollection($this->record));

            DB::commit();

            Notification::make()
                ->title(__('loan.filament.assets_assigned_successfully'))
                ->success()
                ->send();

            $this->redirect(LoanApplicationResource::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title(__('loan.filament.assignment_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
