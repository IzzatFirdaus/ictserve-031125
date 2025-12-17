<?php

declare(strict_types=1);

namespace Tests\Unit\Filament;

use App\Filament\Widgets\EnhancedRealTimeDashboardWidget;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnhancedRealTimeDashboardWidgetTest extends TestCase
{
    #[Test]
    public function it_uses_a_sixty_second_cache_ttl(): void
    {
        $widget = new class extends EnhancedRealTimeDashboardWidget
        {
            public function cacheTtl(): int
            {
                return $this->getCacheTtl();
            }

            public function runCached(callable $callback, ?string $suffix = null): mixed
            {
                return $this->cached($callback, $suffix);
            }

            protected function getCacheKeyPrefix(): string
            {
                return 'widget:EnhancedRealTimeDashboardWidget';
            }
        };

        Cache::shouldReceive('remember')
            ->once()
            ->with('widget:EnhancedRealTimeDashboardWidget:test', 60, Mockery::type('callable'))
            ->andReturn('cached-value');

        $this->assertSame(60, $widget->cacheTtl());
        $this->assertSame('cached-value', $widget->runCached(fn () => 'value', 'test'));
    }
}
