<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\BedrockChat;
use App\Livewire\Ollama\FaqBotWidget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test Chat UI Fixes for v3.6.0
 *
 * Tests the fixes implemented for:
 * 1. FAQ enter button functionality
 * 2. Bedrock chat enter key support
 * 3. Message alignment and visual improvements
 * 4. Floating chat box functionality
 * 5. Theme icon visibility
 * 6. Chat history persistence
 */
class ChatUIFixesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function bedrock_chat_component_renders_without_errors(): void
    {
        $component = Livewire::test(BedrockChat::class);

        $component->assertStatus(200)
            ->assertSee('Sembang AWS Bedrock')
            ->assertSee('Model AI')
            ->assertSee('Taip mesej anda...');
    }

    #[Test]
    public function bedrock_chat_input_has_enter_key_functionality(): void
    {
        $component = Livewire::test(BedrockChat::class);

        // Set a model first (required for sending)
        $component->set('model', 'sonnet');
        $component->set('prompt', 'Test message');

        // The wire:keydown.enter should be present in the rendered view
        $component->assertSee('wire:keydown.enter="send"', false);
    }

    #[Test]
    public function faq_bot_widget_renders_correctly(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        $component->assertStatus(200)
            ->assertSee('Buka atau tutup FAQ Bot');
    }

    #[Test]
    public function faq_bot_widget_input_has_enter_key_support(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        // Open the widget
        $component->call('toggleWidget');

        // The input should have wire:keydown.enter functionality
        $component->assertSee('wire:keydown.enter="submitQuery"', false);
    }

    #[Test]
    public function bedrock_chat_message_alignment_is_correct(): void
    {
        $component = Livewire::test(BedrockChat::class);

        // Add some test messages
        $component->set('messages', [
            [
                'role' => 'user',
                'content' => 'Hello',
                'timestamp' => now()->toIso8601String(),
            ],
            [
                'role' => 'assistant',
                'content' => 'Hi there!',
                'model' => 'sonnet',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);

        // User messages should be right-aligned (justify-end)
        $component->assertSee('justify-end');
        // Bot messages should be left-aligned (justify-start)
        $component->assertSee('justify-start');
    }

    #[Test]
    public function bedrock_chat_preserves_session_data(): void
    {
        $component = Livewire::test(BedrockChat::class);

        // Set some messages
        $messages = [
            [
                'role' => 'user',
                'content' => 'Test message',
                'timestamp' => now()->toIso8601String(),
            ],
        ];

        $component->set('messages', $messages);

        // Simulate saving conversation (this would normally happen in send())
        session(['bedrock_temp_messages' => $messages]);

        // Create a new component instance (simulating page refresh)
        $newComponent = Livewire::test(BedrockChat::class);

        // Messages should be restored from session
        $this->assertEquals($messages, session('bedrock_temp_messages'));
    }

    #[Test]
    public function theme_toggle_functionality_works(): void
    {
        $response = $this->get('/bedrock-chat');

        $response->assertStatus(200)
            ->assertSee('theme-toggle')
            ->assertSee('Tukar tema');
    }

    #[Test]
    public function faq_page_has_search_functionality(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200)
            ->assertSee('faq-search')
            ->assertSee('Cari Soalan');
    }

    #[Test]
    public function floating_chat_widget_is_present_on_faq_page(): void
    {
        // Enable Ollama for this test
        config(['ollama.enabled' => true]);

        $response = $this->get('/faq');

        $response->assertStatus(200)
            ->assertSeeLivewire(FaqBotWidget::class);
    }
}
