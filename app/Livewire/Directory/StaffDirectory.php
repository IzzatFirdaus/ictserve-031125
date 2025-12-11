<?php

declare(strict_types=1);

namespace App\Livewire\Directory;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * ICT Staff Directory Component
 *
 * Displays BPM ICT support contact information and office hours.
 * Part of the guest-accessible public pages.
 *
 * @trace D12 §5.1 - Guest Layout
 * @trace figma-ui-redesign Requirements 31
 *
 * @wcag SC 1.3.1 Info and Relationships (Level A)
 */
#[Layout('layouts.landing')]
class StaffDirectory extends Component
{
    /**
     * ICT Support contacts organized by category.
     *
     * @var array<string, array<int, array{name: string, role: string, email: string, phone: string, extension: string}>>
     */
    public array $contacts = [];

    /**
     * Office hours information.
     *
     * @var array<string, string>
     */
    public array $officeHours = [];

    /**
     * Location information.
     *
     * @var array<string, string>
     */
    public array $location = [];

    public function mount(): void
    {
        $this->contacts = [
            'helpdesk' => [
                [
                    'name' => __('directory.helpdesk_name'),
                    'role' => __('directory.helpdesk_role'),
                    'email' => 'ict.helpdesk@motac.gov.my',
                    'phone' => '+603-8000 8000',
                    'extension' => '8100',
                ],
            ],
            'network' => [
                [
                    'name' => __('directory.network_name'),
                    'role' => __('directory.network_role'),
                    'email' => 'ict.network@motac.gov.my',
                    'phone' => '+603-8000 8000',
                    'extension' => '8101',
                ],
            ],
            'systems' => [
                [
                    'name' => __('directory.systems_name'),
                    'role' => __('directory.systems_role'),
                    'email' => 'ict.systems@motac.gov.my',
                    'phone' => '+603-8000 8000',
                    'extension' => '8102',
                ],
            ],
            'assets' => [
                [
                    'name' => __('directory.assets_name'),
                    'role' => __('directory.assets_role'),
                    'email' => 'ict.assets@motac.gov.my',
                    'phone' => '+603-8000 8000',
                    'extension' => '8103',
                ],
            ],
        ];

        $this->officeHours = [
            'weekdays' => __('directory.hours_weekdays'),
            'lunch' => __('directory.hours_lunch'),
            'friday_prayer' => __('directory.hours_friday_prayer'),
            'closed' => __('directory.hours_closed'),
        ];

        $this->location = [
            'building' => __('directory.location_building'),
            'ministry' => __('directory.location_ministry'),
            'address' => __('directory.location_address'),
            'city' => __('directory.location_city'),
            'country' => __('directory.location_country'),
        ];
    }

    public function render(): \Illuminate\View\View: \Illuminate\View\View
    {
        return view('livewire.directory.staff-directory');
    }
}
