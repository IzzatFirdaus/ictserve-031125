<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminLoginPageViewTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_login_page_displays_branding_footer_and_chatbot_slot(): void
    {
        app()->setLocale('ms');
        config(['ollama.enabled' => true]);

        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('ICTServe', false);
        $response->assertSee(trans('auth.need_help'), false);
        $response->assertSee(trans('auth.contact_support'), false);
        $response->assertSee(trans('common.all_rights_reserved'), false);
        $response->assertSee('faq-bot-widget', false);
    }
}
