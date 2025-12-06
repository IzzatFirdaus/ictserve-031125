<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RealtimeConfigurationTest extends TestCase
{
    #[Test]
    public function connect_src_allows_reverb_websocket_hosts(): void
    {
        $originalConfig = [
            'app.env' => Config::get('app.env'),
            'reverb.apps.apps.0.options.host' => Config::get('reverb.apps.apps.0.options.host'),
            'reverb.apps.apps.0.options.port' => Config::get('reverb.apps.apps.0.options.port'),
            'reverb.servers.reverb.host' => Config::get('reverb.servers.reverb.host'),
            'reverb.servers.reverb.port' => Config::get('reverb.servers.reverb.port'),
        ];

        Config::set('app.env', 'local');
        Config::set('reverb.apps.apps.0.options.host', '127.0.0.1');
        Config::set('reverb.apps.apps.0.options.port', 8080);
        Config::set('reverb.servers.reverb.host', '0.0.0.0');
        Config::set('reverb.servers.reverb.port', 8080);

        try {
            $middleware = new SecurityHeadersMiddleware;
            $request = Request::create('/', 'GET');

            $response = $middleware->handle($request, fn () => response('OK'));

            $policy = $response->headers->get('Content-Security-Policy');

            $this->assertNotNull($policy);
            $this->assertStringContainsString('ws://127.0.0.1:8080', $policy);
            $this->assertStringContainsString('wss://127.0.0.1:8080', $policy);
        } finally {
            foreach ($originalConfig as $key => $value) {
                Config::set($key, $value);
            }
        }
    }

    #[Test]
    public function livewire_scripts_use_configured_asset_url(): void
    {
        $originalAssetUrl = Config::get('livewire.asset_url');
        Config::set('livewire.asset_url', 'http://localhost/ictserve-031125/public/livewire/livewire.js');

        try {
            $scripts = FrontendAssets::js([]);

            $this->assertStringContainsString(
                'src="http://localhost/ictserve-031125/public/livewire/livewire.js?id=',
                $scripts
            );
        } finally {
            Config::set('livewire.asset_url', $originalAssetUrl);
        }
    }
}
