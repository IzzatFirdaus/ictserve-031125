<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlpineAssetRegistrationTest extends TestCase
{
    #[Test]
    public function app_js_registers_alpine_patterns(): void
    {
        $appJsPath = resource_path('js/app.js');
        $this->assertFileExists($appJsPath);

        $contents = File::get($appJsPath);

        $this->assertStringContainsString(
            'import "./alpine-patterns";',
            $contents,
            'Alpine patterns should be imported in the Vite entry.'
        );
    }
}
