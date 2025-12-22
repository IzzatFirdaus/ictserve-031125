<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Dashboard\WidgetCustomizationPanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Widget Customization Feature Tests
 *
 * Tests the widget customization panel functionality including
 * drag-and-drop, visibility toggles, size options, and user preferences.
 *
 * @trace Requirements: R5 (Widget Configuration), R20 (Widget Customization)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetCustomizationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'dashboard_layout' => null,
        ]);
    }

    #[Test]
    public function it_can_render_widget_customization_panel(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->assertStatus(200)
            ->assertSee('Penyesuaian Widget');
    }

    #[Test]
    public function it_can_toggle_panel_visibility(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->assertSet('isOpen', false)
            ->call('togglePanel')
            ->assertSet('isOpen', true)
            ->call('togglePanel')
            ->assertSet('isOpen', false);
    }

    #[Test]
    public function it_can_switch_between_tabs(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->assertSet('activeTab', 'layout')
            ->call('switchTab', 'visibility')
            ->assertSet('activeTab', 'visibility')
            ->call('switchTab', 'sizes')
            ->assertSet('activeTab', 'sizes')
            ->call('switchTab', 'import-export')
            ->assertSet('activeTab', 'import-export');
    }

    #[Test]
    public function it_loads_user_layout_on_mount(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(WidgetCustomizationPanel::class);

        $this->assertIsArray($component->get('layout'));
        $this->assertArrayHasKey('widgets', $component->get('layout'));
    }

    #[Test]
    public function it_can_export_layout(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(WidgetCustomizationPanel::class)
            ->call('exportLayout')
            ->assertSet('showExportModal', true);

        $this->assertNotEmpty($component->get('exportData'));
    }

    #[Test]
    public function it_can_reset_to_default_layout(): void
    {
        // Set a custom layout first
        $customLayout = [
            'version' => '1.0',
            'widgets' => ['header' => [], 'content' => [], 'charts' => []],
            'hidden_widgets' => ['SomeWidget'],
            'widget_sizes' => [],
        ];

        $this->user->update(['dashboard_layout' => $customLayout]);

        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->call('resetToDefault')
            ->assertHasNoErrors();

        // Verify the layout was reset
        $this->user->refresh();
        $layout = $this->user->dashboard_layout;

        $this->assertIsArray($layout);
        $this->assertEmpty($layout['hidden_widgets'] ?? []);
    }

    #[Test]
    public function it_validates_invalid_tab_names(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->assertSet('activeTab', 'layout')
            ->call('switchTab', 'invalid-tab')
            ->assertSet('activeTab', 'layout'); // Should remain unchanged
    }

    #[Test]
    public function it_handles_unauthenticated_users_gracefully(): void
    {
        Livewire::test(WidgetCustomizationPanel::class)
            ->call('toggleWidgetVisibility', 'SomeWidget')
            ->assertHasErrors(['general']);
    }

    #[Test]
    public function it_can_close_modals(): void
    {
        $this->actingAs($this->user);

        Livewire::test(WidgetCustomizationPanel::class)
            ->set('showExportModal', true)
            ->set('showImportModal', true)
            ->set('showResetConfirmation', true)
            ->call('closeModal', 'export')
            ->assertSet('showExportModal', false)
            ->call('closeModal', 'import')
            ->assertSet('showImportModal', false)
            ->call('closeModal', 'reset')
            ->assertSet('showResetConfirmation', false);
    }

    #[Test]
    public function it_provides_proper_display_names(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(WidgetCustomizationPanel::class);

        // Test category display names
        $this->assertEquals('Statistik Utama', $component->instance()->getCategoryDisplayName('header'));
        $this->assertEquals('Kandungan', $component->instance()->getCategoryDisplayName('content'));
        $this->assertEquals('Carta dan Graf', $component->instance()->getCategoryDisplayName('charts'));

        // Test size display names
        $this->assertEquals('Kecil', $component->instance()->getSizeDisplayName('small'));
        $this->assertEquals('Sederhana', $component->instance()->getSizeDisplayName('medium'));
        $this->assertEquals('Besar', $component->instance()->getSizeDisplayName('large'));

        // Test widget display name conversion
        $displayName = $component->instance()->getWidgetDisplayName('App\\Filament\\Widgets\\TestWidget');
        $this->assertIsString($displayName);
    }
}
