<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_header_uses_motac_branding(): void
    {
        $user = User::factory()->create();

        $this->blade('<x-layout.auth-header :user="$user" />', ['user' => $user])
            ->assertSee('images/jata-negara.svg', false)
            ->assertSee(__('common.jata_negara'), false)
            ->assertSee(__('common.motac_logo'), false);
    }

    public function test_application_logo_component_renders_motac_asset(): void
    {
        $this->blade('<x-application-logo class="h-8" />')
            ->assertSee('images/motac-logo.png', false)
            ->assertSee(__('common.motac_logo'), false);
    }

    public function test_mail_header_component_uses_motac_logo(): void
    {
        $html = view('vendor.mail.html.header', [
            'url' => config('app.url'),
            'slot' => 'Laravel',
        ])->render();

        $this->assertStringContainsString('images/motac-logo.png', $html);
        $this->assertStringContainsString(__('common.motac_logo'), $html);
    }
}
