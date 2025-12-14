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
 * Displays role-based quick actions for common workflows.
 *
 * Features:
 * - Create ticket shortcut
 * - Manage loans shortcut
 * - Manage assets shortcut
 * - Permission-based visibility
 *
 * @trace Requirements: 8.4, 10.2
 *
 * @see D04 §3.2 Dashboard widgets
 */
class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions';

    protected int|string|array $columnSpan = 'full';

    /**
     * Sort order - display after critical alerts
     */
    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'Cipta Tiket',
                    'icon' => 'heroicon-o-ticket',
                    'color' => 'primary',
                    'url' => HelpdeskTicketResource::getUrl('create'),
                    'permission' => 'create_helpdesk_ticket',
                    'description' => 'Daftarkan insiden atau permohonan perkhidmatan baharu untuk pasukan helpdesk ICTServe.',
                ],
                [
                    'label' => 'Urus Pinjaman',
                    'icon' => 'heroicon-o-cube',
                    'color' => 'warning',
                    'url' => LoanApplicationResource::getUrl('index'),
                    'permission' => 'view_loan_application',
                    'description' => 'Semak, luluskan atau tolak permohonan pinjaman aset daripada staf.',
                ],
                [
                    'label' => 'Urus Aset',
                    'icon' => 'heroicon-o-computer-desktop',
                    'color' => 'success',
                    'url' => AssetResource::getUrl('index'),
                    'permission' => 'view_asset',
                    'description' => 'Akses inventori aset untuk mengemas kini status kitar hayat atau tugasan.',
                ],
            ],
        ];
    }
}
