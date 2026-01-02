<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Filament\Facades\Filament;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Alias Resource Redirect Integration Tests
 *
 * Tests the RedirectAliasResources middleware behavior for redirecting
 * deprecated alias resource URLs to their canonical counterparts.
 *
 * @see App\Http\Middleware\RedirectAliasResources
 * @see Requirements 36.1, 36.2, 36.3
 */
class AliasResourceRedirectTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->admin->assignRole('admin');
    }

    /**
     * Test redirect from alias URL to canonical URL.
     *
     * @see Requirements 36.1
     */
    #[Test]
    public function alias_loan_applications_url_redirects_to_canonical_url(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin/operations/loans/loan-applications');

        $response->assertRedirect('/admin/operations/loan-applications');
    }

    /**
     * Test redirect uses HTTP 301 permanent redirect status code.
     *
     * @see Requirements 36.2
     */
    #[Test]
    public function alias_redirect_uses_301_status_code(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin/operations/loans/loan-applications');

        $response->assertStatus(301);
    }

    /**
     * Test query parameters are preserved during redirect.
     *
     * @see Requirements 36.3
     */
    #[Test]
    public function alias_redirect_preserves_query_parameters(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin/operations/loans/loan-applications?page=2&sort=created_at');

        $response->assertStatus(301);
        $response->assertRedirect('/admin/operations/loan-applications?page=2&sort=created_at');
    }

    /**
     * Test redirect works for sub-routes (view page).
     *
     * @see Requirements 36.1
     */
    #[Test]
    public function alias_redirect_works_for_view_sub_route(): void
    {
        $this->actingAsForFilament($this->admin);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $response = $this->get("/admin/operations/loans/loan-applications/{$application->id}");

        $response->assertStatus(301);
        $response->assertRedirect("/admin/operations/loan-applications/{$application->id}");
    }

    /**
     * Test redirect works for sub-routes (edit page).
     *
     * @see Requirements 36.1
     */
    #[Test]
    public function alias_redirect_works_for_edit_sub_route(): void
    {
        $this->actingAsForFilament($this->admin);

        $division = Division::factory()->create();
        $application = LoanApplication::factory()->create(['division_id' => $division->id]);

        $response = $this->get("/admin/operations/loans/loan-applications/{$application->id}/edit");

        $response->assertStatus(301);
        $response->assertRedirect("/admin/operations/loan-applications/{$application->id}/edit");
    }

    /**
     * Test redirect works for create sub-route.
     *
     * @see Requirements 36.1
     */
    #[Test]
    public function alias_redirect_works_for_create_sub_route(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin/operations/loans/loan-applications/create');

        $response->assertStatus(301);
        $response->assertRedirect('/admin/operations/loan-applications/create');
    }

    /**
     * Test redirect preserves complex query parameters with filters.
     *
     * @see Requirements 36.3
     */
    #[Test]
    public function alias_redirect_preserves_complex_filter_parameters(): void
    {
        $this->actingAsForFilament($this->admin);

        $queryString = 'tableFilters[status][value]=approved&tableFilters[priority][value]=high&page=1';
        $response = $this->get("/admin/operations/loans/loan-applications?{$queryString}");

        $response->assertStatus(301);

        // Get the redirect location and parse it
        $redirectUrl = $response->headers->get('Location');
        $parsedUrl = parse_url($redirectUrl);

        // Verify the path is correct
        $this->assertEquals('/admin/operations/loan-applications', $parsedUrl['path']);

        // Verify query parameters are preserved (may be URL-encoded or reordered)
        parse_str($parsedUrl['query'] ?? '', $redirectParams);
        $this->assertEquals('approved', $redirectParams['tableFilters']['status']['value']);
        $this->assertEquals('high', $redirectParams['tableFilters']['priority']['value']);
        $this->assertEquals('1', $redirectParams['page']);
    }

    /**
     * Test non-alias URLs are not affected by the middleware.
     */
    #[Test]
    public function canonical_url_is_not_redirected(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin/operations/loan-applications');

        // Should not be a redirect, should be successful
        $response->assertSuccessful();
    }

    /**
     * Test other admin URLs are not affected by the middleware.
     */
    #[Test]
    public function other_admin_urls_are_not_redirected(): void
    {
        $this->actingAsForFilament($this->admin);

        $response = $this->get('/admin');

        // Should not be a redirect
        $this->assertNotEquals(301, $response->getStatusCode());
    }

    /**
     * Helper method to authenticate user for Filament.
     */
    private function actingAsForFilament(User $user): void
    {
        $this->actingAs($user);
        Filament::auth()->login($user);
    }
}
