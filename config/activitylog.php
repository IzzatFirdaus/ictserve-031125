<?php

declare(strict_types=1);

use Spatie\Activitylog\Models\Activity;

/**
 * Spatie Laravel Activitylog Configuration
 *
 * Part of the ICTServe v3.5.0 Dual Audit System
 * This package handles operational/user activity logging
 * while owen-it/laravel-auditing handles field-level compliance auditing.
 *
 * @see D09 §4.7 - Activity Log Requirements
 * @see D00 §4.1 - Dual Audit System Architecture
 */

return [

    /*
     * If set to false, no activities will be saved to the database.
     * Enable activity logging for all environments.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     *
     * Per D02 §8.2 and PDPA/Arkib Negara requirements:
     * Retain audit records for 7 years (2555 days)
     */
    'delete_records_older_than_days' => 2555,

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     *
     * ICTServe uses categorized log names:
     * - 'default' for general activities
     * - 'helpdesk' for helpdesk module activities
     * - 'loan' for asset loan module activities
     * - 'auth' for authentication activities
     * - 'admin' for admin panel activities
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     * Enable to track activities on soft-deleted records.
     */
    'subject_returns_soft_deleted_models' => true,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => env('ACTIVITY_LOGGER_TABLE_NAME', 'activity_log'),

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
];
