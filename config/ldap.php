<?php

declare(strict_types=1);

/**
 * LDAP/Active Directory Configuration
 *
 * PKS 5.2.1 Compliance - LDAP/AD SSO Authentication
 *
 * This configuration supports MOTAC Active Directory integration
 * for mandatory SSO authentication per PKS Accountability requirements.
 *
 * @see D03-FR-001.1 (Authenticated access only)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @trace Requirements 1.1, 2.3, 5.2, 27.1
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default LDAP Connection Name
    |--------------------------------------------------------------------------
    |
    | The default LDAP connection to use for authentication.
    |
    */

    'default' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    |
    | Configure LDAP connections for MOTAC Active Directory.
    |
    */

    'connections' => [

        'default' => [
            'hosts' => explode(',', env('LDAP_HOSTS', 'ldap.motac.gov.my')),
            'username' => env('LDAP_USERNAME'),
            'password' => env('LDAP_PASSWORD'),
            'port' => (int) env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN', 'dc=motac,dc=gov,dc=my'),
            'timeout' => (int) env('LDAP_TIMEOUT', 5),
            'use_ssl' => (bool) env('LDAP_SSL', false),
            'use_tls' => (bool) env('LDAP_TLS', true),
            'version' => (int) env('LDAP_VERSION', 3),
            'follow_referrals' => false,

            // LDAP Options (only set if LDAP extension is loaded)
            'options' => extension_loaded('ldap') ? [
                LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER,
            ] : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Logging
    |--------------------------------------------------------------------------
    |
    | Enable LDAP query logging for debugging and audit purposes.
    |
    */

    'logging' => [
        'enabled' => (bool) env('LDAP_LOGGING', true),
        'channel' => env('LDAP_LOG_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Cache
    |--------------------------------------------------------------------------
    |
    | Cache LDAP queries to improve performance.
    |
    */

    'cache' => [
        'enabled' => (bool) env('LDAP_CACHE', true),
        'driver' => env('LDAP_CACHE_DRIVER', 'file'),
        'ttl' => (int) env('LDAP_CACHE_TTL', 300), // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | User Attribute Mapping
    |--------------------------------------------------------------------------
    |
    | Map LDAP attributes to User model attributes.
    |
    */

    'user_attributes' => [
        'guid' => 'objectguid',
        'name' => 'cn',
        'email' => 'mail',
        'username' => 'samaccountname',
        'staff_id' => 'employeeid',
        'department' => 'department',
        'title' => 'title',
        'phone' => 'telephonenumber',
        'mobile' => 'mobile',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Settings
    |--------------------------------------------------------------------------
    |
    | PKS 5.4.3 Password Policy Compliance
    |
    */

    'authentication' => [
        // User filter for authentication
        'filter' => env('LDAP_USER_FILTER', '(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))'),

        // Allowed domains for email authentication
        'allowed_domains' => explode(',', env('LDAP_ALLOWED_DOMAINS', 'motac.gov.my')),

        // PKS 5.4.3 Password Policy
        'password_policy' => [
            'min_length' => 8,
            'max_age_days' => 90,
            'lockout_threshold' => 3,
            'lockout_duration_minutes' => 30,
        ],

        // Sync user data on login
        'sync_on_login' => (bool) env('LDAP_SYNC_ON_LOGIN', true),

        // Create user if not exists
        'create_user' => (bool) env('LDAP_CREATE_USER', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Authentication
    |--------------------------------------------------------------------------
    |
    | Fallback to local authentication if LDAP is unavailable.
    | Should be disabled in production for PKS compliance.
    |
    */

    'fallback' => [
        'enabled' => (bool) env('LDAP_FALLBACK_ENABLED', false),
        'driver' => 'eloquent',
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Scopes
    |--------------------------------------------------------------------------
    |
    | Define LDAP search scopes for different user types.
    |
    */

    'scopes' => [
        'users' => env('LDAP_USERS_DN', 'ou=Users,dc=motac,dc=gov,dc=my'),
        'groups' => env('LDAP_GROUPS_DN', 'ou=Groups,dc=motac,dc=gov,dc=my'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Group Mapping
    |--------------------------------------------------------------------------
    |
    | Map LDAP groups to application roles.
    |
    */

    'group_mapping' => [
        'ICTServe-Admins' => 'admin',
        'ICTServe-Superusers' => 'superuser',
        'ICTServe-Approvers' => 'approver',
        'ICTServe-Staff' => 'staff',
        'Domain Users' => 'staff',
    ],

];
