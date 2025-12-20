<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use Illuminate\Support\Collection;
use Tests\TestCase;

class PortalLayoutComponentsTest extends TestCase
{
    public function test_portal_layout_renders_core_regions(): void
    {
        $view = $this->view('portal.layouts.app', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/portal/dashboard'],
                ['label' => 'Submissions'],
            ],
        ]);

        $view->assertSee('portal-primary-navigation');
        $view->assertSee('portal-sidebar');
        $view->assertSee('portal-footer');
        $view->assertSee('main-content');
    }

    public function test_portal_data_rights_pages_render_with_layout(): void
    {
        $indexView = $this->view('portal.data-rights.index');
        $indexView->assertSee('portal-primary-navigation');
        $indexView->assertSee('portal-footer');

        $consentView = $this->view('portal.data-rights.consent-history', [
            'consents' => Collection::make(),
        ]);
        $consentView->assertSee('portal-primary-navigation');
        $consentView->assertSee('portal-footer');
    }
}
