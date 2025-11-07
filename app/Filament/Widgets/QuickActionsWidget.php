<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Filament\Resources\Loans\LoanApplicationResource;
use Filament\Widgets\Widget;

/**
 * Quick Actions Widget
 *
 * Provides one-click access to common tasks with modal forms.
 *
 * @trace Requirements 6.4
 */
class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'Create Ticket',
                    'icon' => 'heroicon-o-ticket',
                    'color' => 'primary',
                    'url' => HelpdeskTicketResource::getUrl('create'),
                    'permission' => 'create_helpdesk_ticket',
                ],
                [
                    'label' => 'Process Loan',
                    'icon' => 'heroicon-o-cube',
                    'color' => 'warning',
                    'url' => LoanApplicationResource::getUrl('index'),
                    'permission' => 'view_loan_application',
                ],
                [
                    'label' => 'Manage Assets',
                    'icon' => 'heroicon-o-computer-desktop',
                    'color' => 'success',
                    'url' => AssetResource::getUrl('index'),
                    'permission' => 'view_asset',
                ],
            ],
        ];
    }
}
