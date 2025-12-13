<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Ollama\FaqBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FaqBotTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function faq_bot_component_can_be_rendered(): void
    {
        Livewire::test(FaqBot::class)
            ->assertStatus(200)
            ->assertSee('FAQ Bot AI')
            ->assertSee('Tanya soalan anda');
    }

    #[Test]
    public function faq_bot_validates_required_query(): void
    {
        Livewire::test(FaqBot::class)
            ->set('query', '')
            ->call('submitQuery')
            ->assertHasErrors(['query' => 'required']);
    }

    #[Test]
    public function faq_bot_validates_query_max_length(): void
    {
        $longQuery = str_repeat('a', 501);

        Livewire::test(FaqBot::class)
            ->set('query', $longQuery)
            ->call('submitQuery')
            ->assertHasErrors(['query' => 'max']);
    }

    #[Test]
    public function faq_bot_can_clear_conversation(): void
    {
        Livewire::test(FaqBot::class)
            ->set('messages', [
                ['role' => 'user', 'content' => 'Test message', 'timestamp' => now()->toIso8601String()],
            ])
            ->call('clearConversation')
            ->assertSet('messages', function ($messages) {
                return count($messages) === 1 && $messages[0]['role'] === 'assistant';
            });
    }

    #[Test]
    public function faq_bot_can_toggle_history(): void
    {
        Livewire::test(FaqBot::class)
            ->assertSet('showHistory', false)
            ->call('toggleHistory')
            ->assertSet('showHistory', true)
            ->call('toggleHistory')
            ->assertSet('showHistory', false);
    }
}
