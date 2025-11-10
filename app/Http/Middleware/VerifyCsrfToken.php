<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

/**
 * Application CSRF middleware placeholder for Laravel 12 streamlined structure.
 * Tests reference this FQCN explicitly (SecurityComplianceValidationTest).
 * Extends the framework's BaseVerifier without custom exclusions.
 */
class VerifyCsrfToken extends BaseVerifier
{
    /**
     * URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];
}
