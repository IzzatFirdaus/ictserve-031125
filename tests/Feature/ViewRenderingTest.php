<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Widgets\CriticalAlertsWidget;
use App\Livewire\BedrockChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_view_compiles(): void
    {
        $this->assertIsString(view('pages.faq')->render());
    }

    public function test_offline_page_view_compiles(): void
    {
        $this->assertIsString(view('pages.offline')->render());
    }

    public function test_offline_indicator_component_view_compiles(): void
    {
        $this->assertIsString(view('components.responsive.offline-indicator')->render());
    }

    public function test_filament_critical_alerts_widget_view_compiles(): void
    {
        Livewire::test(CriticalAlertsWidget::class)
            ->assertStatus(200);
    }

    public function test_bedrock_chat_renders_assistant_markdown(): void
    {
        Livewire::test(BedrockChat::class)
            ->set('messages', [
                [
                    'role' => 'assistant',
                    'content' => '**Bold**',
                    'model' => 'system',
                ],
            ])
            ->assertSee('<strong>Bold</strong>', false)
            ->assertStatus(200);
    }
}
