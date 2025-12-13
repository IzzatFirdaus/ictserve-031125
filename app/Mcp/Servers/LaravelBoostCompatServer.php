<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use Laravel\Boost\Mcp\Boost;
use Laravel\Mcp\Server\Methods\Initialize;
use Laravel\Mcp\Server\ServerContext;
use Laravel\Mcp\Server\Transport\JsonRpcRequest;

class LaravelBoostCompatServer extends Boost
{
    protected function handleInitializeMessage(JsonRpcRequest $request, ServerContext $context): void
    {
        $requestedVersion = $request->params['protocolVersion'] ?? null;

        if (is_string($requestedVersion) && ! in_array($requestedVersion, $context->supportedProtocolVersions, true)) {
            $request->params['protocolVersion'] = $this->negotiateProtocolVersion(
                $requestedVersion,
                $context->supportedProtocolVersions,
            );
        }

        $response = (new Initialize)->handle($request, $context);

        $this->transport->send($response->toJson(), $this->generateSessionId());
    }

    /**
     * @param  array<int, string>  $supportedProtocolVersions
     */
    private function negotiateProtocolVersion(string $requestedVersion, array $supportedProtocolVersions): string
    {
        foreach ($supportedProtocolVersions as $supported) {
            if ($supported <= $requestedVersion) {
                return $supported;
            }
        }

        return $supportedProtocolVersions[array_key_last($supportedProtocolVersions)];
    }
}
