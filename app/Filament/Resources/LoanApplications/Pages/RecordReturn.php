<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Pages;

use App\Enums\LoanStatus;
use App\Filament\Resources\LoanApplications\LoanApplicationResource;
use App\Mail\Loan\AssetReturnConfirmation;
use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\LoanApplication;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * @property Schema $form
 */
class RecordReturn extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = LoanApplicationResource::class;

    protected string $view = 'filament.resources.loan-applications.pages.record-return';

    /** @var array<string, mixed> */
    public array $data = [];

    public Schema $form;

    public LoanApplication $record;

    public function mount(int|string $record): void
    {
        $this->record = LoanApplication::findOrFail($record);

        // Get issued assets for this loan
        /** @var Collection<int, AssetTransaction> $issuedAssets */
        $issuedAssets = AssetTransaction::where('loan_application_id', $this->record->id)
            ->where('transaction_type', 'loan_issue')
            ->with('asset')
            ->get();

        $assets = [];

        foreach ($issuedAssets as $transaction) {
            /** @var AssetTransaction $transaction */
            $assetAccessories = $transaction->asset->accessories ?? [];

            $assets[] = [
                'asset_transaction_id' => $transaction->id,
                'asset_serial_number' => $transaction->getAttribute('asset_serial_number') ?? $transaction->asset?->serial_number,
                'asset_name' => $transaction->asset->name ?? 'N/A',
                'issued_condition' => $transaction->getAttribute('asset_condition') ?? 'good',
                'return_condition' => 'good',
                'maintenance_required' => false,
                'maintenance_notes' => null,
                'available_accessories' => $assetAccessories,
                'returned_accessories' => $assetAccessories, // Default to all returned
            ];
        }

        $this->form->fill([
            'return_date' => now()->format('Y-m-d'),
            'assets' => $assets,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                DatePicker::make('return_date')
                    ->label(__('loan.filament.return_date'))
                    ->required()
                    ->default(now())
                    ->maxDate(now()),
                Repeater::make('assets')
                    ->label(__('loan.filament.assets_returned'))
                    ->schema([
                        TextInput::make('asset_serial_number')
                            ->label(__('loan.filament.serial_number'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('asset_name')
                            ->label(__('loan.filament.asset_name'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('issued_condition')
                            ->label(__('loan.filament.issued_condition'))
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('return_condition')
                            ->label(__('loan.filament.return_condition'))
                            ->options([
                                'excellent' => __('loan.filament.condition.excellent'),
                                'good' => __('loan.filament.condition.good'),
                                'fair' => __('loan.filament.condition.fair'),
                                'damaged' => __('loan.filament.condition.damaged'),
                            ])
                            ->required()
                            ->default('good'),
                        \Filament\Forms\Components\CheckboxList::make('returned_accessories')
                            ->label(__('loan.filament.accessories_returned'))
                            ->options(fn (callable $get) => array_combine($get('available_accessories') ?? [], $get('available_accessories') ?? []))
                            ->visible(fn (callable $get) => ! empty($get('available_accessories')))
                            ->columns(2),
                        Checkbox::make('maintenance_required')
                            ->label(__('loan.filament.maintenance_required'))
                            ->reactive()
                            ->default(false),
                        Textarea::make('maintenance_notes')
                            ->label(__('loan.filament.maintenance_notes'))
                            ->visible(fn (callable $get): bool => $get('maintenance_required'))
                            ->required(fn (callable $get): bool => $get('maintenance_required'))
                            ->rows(3),
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
            Action::make('record')
                ->label(__('loan.filament.confirm_return'))
                ->color('success')
                ->requiresConfirmation()
                ->action('recordReturn'),
        ];
    }

    public function recordReturn(): void
    {
        $data = $this->form->getState();

        if (! isset($data['assets']) || ! is_array($data['assets'])) {
            return;
        }

        $returnConditionForEmail = null;

        try {
            DB::beginTransaction();

            foreach ($data['assets'] as $assetData) {
                /**
                 * @var array{
                 *     asset_transaction_id:int,
                 *     asset_serial_number?:string|null,
                 *     asset_name?:string|null,
                 *     issued_condition?:string|null,
                 *     return_condition?:string,
                 *     maintenance_required?:bool,
                 *     maintenance_notes?:string|null,
                 *     returned_accessories?:array
                 * } $assetData
                 */
                if (! isset($assetData['asset_transaction_id'])) {
                    continue;
                }

                // Create return transaction record
                $issueTransaction = AssetTransaction::find($assetData['asset_transaction_id']);

                if (! $issueTransaction) {
                    continue;
                }

                // Check for missing accessories
                $missingAccessories = array_diff(
                    $assetData['available_accessories'] ?? [],
                    $assetData['returned_accessories'] ?? []
                );

                $notes = $assetData['maintenance_notes'] ?? null;
                if (! empty($missingAccessories)) {
                    $missingStr = implode(', ', $missingAccessories);
                    $notes = ($notes ? $notes."\n" : '').'Missing accessories: '.$missingStr;
                }

                AssetTransaction::create([
                    'asset_id' => $issueTransaction->asset_id,
                    'loan_application_id' => $this->record->id,
                    'transaction_type' => 'loan_return',
                    'asset_serial_number' => $assetData['asset_serial_number'] ?? null,
                    'asset_condition' => $assetData['return_condition'] ?? 'good',
                    'return_date' => $data['return_date'],
                    'received_by_staff_id' => \Illuminate\Support\Facades\Auth::id(),
                    'maintenance_required' => (bool) ($assetData['maintenance_required'] ?? false),
                    'maintenance_notes' => $notes,
                    'transaction_date' => now(),
                ]);

                $returnCondition = (string) ($assetData['return_condition'] ?? 'good');
                $returnConditionForEmail ??= $returnCondition;

                // Update asset status
                $asset = Asset::find($issueTransaction->asset_id);
                if ($asset) {
                    $newStatus = ($assetData['maintenance_required'] ?? false) ? 'under_maintenance' : 'available';
                    $asset->update([
                        'status' => $newStatus,
                        'condition' => $returnCondition ?: $asset->condition,
                    ]);
                }
            }

            // Update loan application status
            $this->record->update([
                'status' => LoanStatus::RETURNED,
                'actual_return_date' => $data['return_date'],
            ]);

            $emailReturnCondition = $returnConditionForEmail ?? 'good';

            // Send confirmation email
            Mail::to($this->record->applicant_email)
                ->send(new AssetReturnConfirmation($this->record, $emailReturnCondition));

            DB::commit();

            Notification::make()
                ->title(__('loan.filament.return_recorded_successfully'))
                ->success()
                ->send();

            $this->redirect(LoanApplicationResource::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title(__('loan.filament.return_recording_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
