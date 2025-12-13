# PHPUnit Test Failures (2025-12-04)

Command: `php artisan test` (timeout: 30 minutes). The run completed the unit suites and a portion of the feature suites before the overall timeout was reached. The following suites still contain failing tests and require code changes:

1. **Tests\Feature\AssetLoan\AuthenticatedPortalTest**
   - `approver_interface_displays_empty_state` (empty state copy missing for approvers with no assignments)

2. **Tests\Feature\AssetLoan\ReportingAnalyticsTest**
   - `report_data_includes_cross_module_metrics`
   - `large_dataset_can_be_retrieved_efficiently_for_export`
   - `cross_module_data_can_be_retrieved_together`
   - `unified_metrics_calculation_is_accurate`

The `Tests\Feature\Auth\AuthenticationTest` suite reported a failure during the long test run (`login screen can be rendered` 500). Re-running that suite in isolation now passes, so the earlier failure was transient.

> **Note:** The test runner timed out before completing the entire feature suite, so there may be additional undiscovered failures beyond this list. Re-running `php artisan test` after addressing the above should continue from the next suite to surface any remaining issues.
