<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Mcp\Servers\LaravelBoostCompatServer;
use Illuminate\Http\Response;
use Laravel\Mcp\Server\Contracts\Transport;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class McpProtocolNegotiationTest extends TestCase
{
    #[Test]
    public function it_negotiates_unknown_protocol_versions_in_initialize(): void
    {
        if (! class_exists(\Laravel\Boost\Mcp\Boost::class)) {
            $this->markTestSkipped('Laravel Boost is not installed.');
        }

        $transport = new class implements Transport
        {
            /** @var array<int, string> */
            public array $messages = [];

            /** @var array<int, string|null> */
            public array $sessionIds = [];

            public function onReceive(\Closure $handler): void
            {
                //
            }

            public function send(string $message, ?string $sessionId = null): void
            {
                $this->messages[] = $message;
                $this->sessionIds[] = $sessionId;
            }

            public function run(): Response|StreamedResponse
            {
                throw new \LogicException('Not implemented.');
            }

            public function sessionId(): ?string
            {
                return 'test-session';
            }

            public function stream(\Closure $stream): void
            {
                //
            }
        };

        $server = new LaravelBoostCompatServer($transport);
        $supported = $server->createContext()->supportedProtocolVersions;

        $requested = '2099-12-31';
        $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => $requested,
                'capabilities' => (object) [],
                'clientInfo' => [
                    'name' => 'phpunit',
                    'version' => '0.0.0',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $this->assertNotEmpty($transport->messages);

        $payload = json_decode($transport->messages[0], true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('result', $payload);
        $this->assertArrayNotHasKey('error', $payload);
        $this->assertContains($payload['result']['protocolVersion'], $supported);
        $this->assertNotSame($requested, $payload['result']['protocolVersion']);
    }

    #[Test]
    public function it_picks_the_latest_supported_version_not_newer_than_requested(): void
    {
        if (! class_exists(\Laravel\Boost\Mcp\Boost::class)) {
            $this->markTestSkipped('Laravel Boost is not installed.');
        }

        $transport = new class implements Transport
        {
            /** @var array<int, string> */
            public array $messages = [];

            public function onReceive(\Closure $handler): void
            {
                //
            }

            public function send(string $message, ?string $sessionId = null): void
            {
                $this->messages[] = $message;
            }

            public function run(): Response|StreamedResponse
            {
                throw new \LogicException('Not implemented.');
            }

            public function sessionId(): ?string
            {
                return 'test-session';
            }

            public function stream(\Closure $stream): void
            {
                //
            }
        };

        $server = new LaravelBoostCompatServer($transport);
        $supported = $server->createContext()->supportedProtocolVersions;

        // Pick a date that's between the first and second supported versions.
        $requested = '2025-04-01';

        $expected = $supported[array_key_last($supported)];
        foreach ($supported as $version) {
            if ($version <= $requested) {
                $expected = $version;
                break;
            }
        }

        $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => $requested,
                'capabilities' => (object) [],
                'clientInfo' => [
                    'name' => 'phpunit',
                    'version' => '0.0.0',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $payload = json_decode($transport->messages[0], true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame($expected, $payload['result']['protocolVersion']);
    }
}
