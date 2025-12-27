<?php

declare(strict_types=1);

namespace Tests\Feature\Components;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FigmaButtonTest extends TestCase
{
    #[Test]
    public function it_renders_figma_button_component(): void
    {
        // Test that the component can be rendered without errors
        $view = view('components.ui.figma-button', [
            'slot' => 'Test Button',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        $html = $view->render();

        $this->assertStringContainsString('Test Button', $html);
        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringContainsString('min-h-11', $html); // WCAG touch target
    }

    #[Test]
    public function it_applies_correct_variant_classes(): void
    {
        // Test primary variant
        $view = view('components.ui.figma-button', [
            'variant' => 'primary',
            'slot' => 'Primary Button',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        $html = $view->render();

        $this->assertStringContainsString('bg-primary-600', $html);
        $this->assertStringContainsString('hover:bg-primary-700', $html);
        $this->assertStringContainsString('text-white', $html);
    }

    #[Test]
    public function it_meets_wcag_accessibility_requirements(): void
    {
        $view = view('components.ui.figma-button', [
            'variant' => 'primary',
            'size' => 'md',
            'slot' => 'Accessible Button',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        $html = $view->render();

        // Check for WCAG 2.2 AA compliance features
        $this->assertStringContainsString('min-h-11', $html); // 44px minimum touch target
        $this->assertStringContainsString('focus:outline-none', $html); // Custom focus handling
        $this->assertStringContainsString('focus-visible:ring-3', $html); // Visible focus indicator (using focus-visible for better UX)
        $this->assertStringContainsString('focus-visible:ring-offset-2', $html); // Focus ring offset
    }

    #[Test]
    public function it_supports_ictserve_design_tokens(): void
    {
        // Test that component uses ICTServe design system tokens
        $view = view('components.ui.figma-button', [
            'variant' => 'primary',
            'slot' => 'ICTServe Button',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        $html = $view->render();

        // Check for ICTServe-specific design tokens
        $this->assertStringContainsString('rounded-lg', $html); // ICTServe radius token
        $this->assertStringContainsString('shadow-button', $html); // ICTServe shadow token
        $this->assertStringContainsString('bg-primary-600', $html); // ICTServe color token
        $this->assertStringContainsString('focus-visible:ring-primary-500', $html); // ICTServe focus token
    }
}
