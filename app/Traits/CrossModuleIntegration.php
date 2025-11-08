<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * CrossModuleIntegration Trait
 *
 * Provides cross-module integration capabilities for services.
 * Used by HybridHelpdeskService and other services that need cross-module functionality.
 *
 * @see D04 Software Design Document - Cross-Module Integration
 * @see updated-helpdesk-module/design.md - Cross-Module Integration Strategy
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
trait CrossModuleIntegration
{
    /**
     * Check if cross-module integration is enabled
     */
    protected function isCrossModuleIntegrationEnabled(): bool
    {
        return config('ictserve.cross_module_integration_enabled', true);
    }

    /**
     * Log cross-module integration event
     */
    protected function logCrossModuleEvent(string $event, array $data): void
    {
        \Illuminate\Support\Facades\Log::info("Cross-Module Integration: {$event}", $data);
    }
}
