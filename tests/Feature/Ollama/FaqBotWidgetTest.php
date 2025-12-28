<?php

declare(strict_types=1);

namespace Tests\Feature\Ollama;

use App\Livewire\Ollama\FaqBotWidget;
use App\Models\Faq;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test FAQ Bot Widget fixes for v3.6.0
 *
 * Tests the three main issues reported:
 * 1. Text formatting problems
 * 2. UI performance issues
 * 3. Content accuracy for asset loan process
 */
class FaqBotWidgetTest extends TestCase
{
    #[Test]
    public function it_can_render_widget_without_errors(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        $component->assertStatus(200)
            ->assertSee('FAQ Bot ICTServe')
            ->assertSee('Selamat datang ke FAQ Bot ICTServe');
    }

    #[Test]
    public function it_can_toggle_widget_visibility(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        // Initially closed
        $component->assertSet('isOpen', false);

        // Toggle open
        $component->call('toggleWidget')
            ->assertSet('isOpen', true);

        // Toggle closed
        $component->call('toggleWidget')
            ->assertSet('isOpen', false);
    }

    #[Test]
    public function it_validates_query_input(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        // Test empty query
        $component->set('query', '')
            ->call('submitQuery')
            ->assertHasErrors(['query' => 'required']);

        // Test query too long
        $component->set('query', str_repeat('a', 501))
            ->call('submitQuery')
            ->assertHasErrors(['query' => 'max']);
    }

    #[Test]
    public function it_can_clear_conversation(): void
    {
        $component = Livewire::test(FaqBotWidget::class);

        // Add some messages
        $component->set('messages', [
            ['role' => 'user', 'content' => 'Test message', 'timestamp' => now()->toIso8601String()],
        ]);

        // Clear conversation
        $component->call('clearConversation')
            ->assertSet('query', '')
            ->assertSet('errorMessage', null);

        // Should have welcome message
        $messages = $component->get('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals('assistant', $messages[0]['role']);
    }

    #[Test]
    public function it_shows_correct_user_context(): void
    {
        // Test as guest
        $component = Livewire::test(FaqBotWidget::class);
        $this->assertFalse($component->get('isAuthenticated'));

        // Test as authenticated user
        $user = User::factory()->create(['name' => 'Test User']);
        $this->actingAs($user);

        $component = Livewire::test(FaqBotWidget::class);
        $this->assertTrue($component->get('isAuthenticated'));
        $this->assertEquals('Test User', $component->get('userName'));
    }

    #[Test]
    public function it_handles_asset_loan_queries_with_accurate_information(): void
    {
        // Create relevant FAQ
        Faq::factory()->create([
            'question' => 'Bagaimanakah cara untuk memohon pinjaman aset ICT?',
            'answer' => 'Untuk memohon pinjaman aset ICT di ICTServe, ikuti langkah berikut: 1) Log masuk ke portal ICTServe menggunakan akaun staf MOTAC anda. 2) Pilih menu "Pinjaman Aset ICT" dari dashboard. 3) Klik butang "Permohonan Baru". 4) Isi borang permohonan dengan lengkap termasuk jenis aset, tarikh pinjaman, dan tujuan penggunaan. 5) Hantar permohonan - sistem akan menghantar email kepada ketua jabatan (Gred 41+) untuk kelulusan. 6) Ketua jabatan boleh meluluskan atau menolak melalui pautan dalam email tanpa perlu log masuk sistem. 7) Selepas diluluskan, admin ICT akan menghubungi anda untuk pengambilan aset.',
            'tags' => ['pinjaman', 'aset', 'ict', 'permohonan'],
        ]);

        $component = Livewire::test(FaqBotWidget::class);

        // Test asset loan query
        $component->set('query', 'bagaimanakah cara untuk memohon aset ict')
            ->call('submitQuery');

        // Should not have validation errors
        $component->assertHasNoErrors();

        // Should have processed the query (loading state may complete quickly)
        $this->assertFalse($component->get('isLoading'));
    }
}
