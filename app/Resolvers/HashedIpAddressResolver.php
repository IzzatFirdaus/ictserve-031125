<?php

declare(strict_types=1);

namespace App\Resolvers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

/**
 * Custom IP Address Resolver that hashes IP addresses for PDPA compliance.
 *
 * This resolver implements privacy-by-design by storing SHA-256 hashed IP addresses
 * instead of raw IPs, meeting PDPA 2010 requirements while maintaining audit trail
 * integrity for compliance purposes.
 *
 * @see D09 §4.6 - Dual Audit System requirements
 * @see Requirements 19.1, 19.3 - Field-level audit with IP hashing
 */
class HashedIpAddressResolver implements Resolver
{
    /**
     * Resolve the hashed IP address.
     *
     * @param  Auditable  $auditable  The auditable model (not used but required by interface)
     * @return string|null The SHA-256 hashed IP address or null if unavailable
     */
    public static function resolve(Auditable $auditable): ?string
    {
        $request = request();

        if (! $request instanceof Request) {
            return null;
        }

        $ipAddress = $request->ip();

        if ($ipAddress === null) {
            return null;
        }

        // Hash the IP address using SHA-256 for privacy compliance
        // This allows audit trail verification without storing PII
        return hash('sha256', $ipAddress.config('app.key'));
    }
}
