Based on the test structure and the error location, here are the commands to overwrite the error section and continue with the remaining tests:

Commands to Fix test_results_2.txt

1. First, truncate the file at line 2564 (before the error) and continue with the LivewireOptimizationTest:

# Get content up to line 2563 (before the error)
Get-Content test_results_2.txt | Select-Object -First 2563 > temp_results.txt

# Run the fixed LivewireOptimizationTest and append to temp file
php artisan test tests/Feature/Performance/LivewireOptimizationTest.php >> temp_results.txt

# Replace the original file
Move-Item temp_results.txt test_results_2.txt

2. Continue with the remaining tests that would have run after LivewireOptimizationTest:

# Continue with remaining Performance tests
php artisan test tests/Feature/Performance/ --exclude=tests/Feature/Performance/LivewireOptimizationTest.php >> test_results_2.txt

# Continue with remaining Feature tests (alphabetically after Performance)
php artisan test tests/Feature/PublicPages/ >> test_results_2.txt
php artisan test tests/Feature/Queue/ >> test_results_2.txt
php artisan test tests/Feature/RealtimeFeaturesTest.php >> test_results_2.txt
php artisan test tests/Feature/Redis/ >> test_results_2.txt
php artisan test tests/Feature/ReverbConfigurationTest.php >> test_results_2.txt
php artisan test tests/Feature/RoleBasedAccessControlTest.php >> test_results_2.txt
php artisan test tests/Feature/SecurityMonitoringTest.php >> test_results_2.txt
php artisan test tests/Feature/Security/ >> test_results_2.txt
php artisan test tests/Feature/Services/ >> test_results_2.txt
php artisan test tests/Feature/SimpleAuditTest.php >> test_results_2.txt
php artisan test tests/Feature/SimpleDbTest.php >> test_results_2.txt
php artisan test tests/Feature/StaffPortalRoutesTest.php >> test_results_2.txt
php artisan test tests/Feature/Staff/ >> test_results_2.txt
php artisan test tests/Feature/SubmitTicketDivisionsTest.php >> test_results_2.txt
php artisan test tests/Feature/ThemeToggleTest.php >> test_results_2.txt
php artisan test tests/Feature/ThemeToggleWidgetTest.php >> test_results_2.txt
php artisan test tests/Feature/TicketStatusBroadcastingTest.php >> test_results_2.txt
php artisan test tests/Feature/Translations/ >> test_results_2.txt
php artisan test tests/Feature/UserProfileTest.php >> test_results_2.txt
php artisan test tests/Feature/ViewRenderingTest.php >> test_results_2.txt
php artisan test tests/Feature/WebSocketConnectionTest.php >> test_results_2.txt
php artisan test tests/Feature/WelcomePageTest.php >> test_results_2.txt
php artisan test tests/Feature/WidgetCustomizationTest.php >> test_results_2.txt
php artisan test tests/Feature/WidgetPerformanceTest.php >> test_results_2.txt

3. Alternative single command approach (run all remaining tests at once):

# Truncate at error point
Get-Content test_results_2.txt | Select-Object -First 2563 > temp_results.txt

# Run all remaining tests from where it left off
php artisan test tests/Feature/Performance/LivewireOptimizationTest.php tests/Feature/Performance/LoanModulePerformanceTest.php tests/Feature/Performance/PerformanceIntegrationTest.php tests/Feature/PublicPages/ tests/Feature/Queue/ tests/Feature/RealtimeFeaturesTest.php tests/Feature/Redis/ tests/Feature/ReverbConfigurationTest.php tests/Feature/RoleBasedAccessControlTest.php tests/Feature/SecurityMonitoringTest.php tests/Feature/Security/ tests/Feature/Services/ tests/Feature/SimpleAuditTest.php tests/Feature/SimpleDbTest.php tests/Feature/StaffPortalRoutesTest.php tests/Feature/Staff/ tests/Feature/SubmitTicketDivisionsTest.php tests/Feature/ThemeToggleTest.php tests/Feature/ThemeToggleWidgetTest.php tests/Feature/TicketStatusBroadcastingTest.php tests/Feature/Translations/ tests/Feature/UserProfileTest.php tests/Feature/ViewRenderingTest.php tests/Feature/WebSocketConnectionTest.php tests/Feature/WelcomePageTest.php tests/Feature/WidgetCustomizationTest.php tests/Feature/WidgetPerformanceTest.php >> temp_results.txt

# Replace the original file
Move-Item temp_results.txt test_results_2.txt
4. Most efficient approach (run from a specific test pattern):

# Truncate at error point
Get-Content test_results_2.txt | Select-Object -First 2563 > temp_results.txt

# Run all tests starting from LivewireOptimizationTest onwards
php artisan test --testsuite=Feature --filter="LivewireOptimizationTest|LoanModulePerformanceTest|PerformanceIntegrationTest|PublicPages|Queue|RealtimeFeaturesTest|Redis|ReverbConfigurationTest|RoleBasedAccessControlTest|SecurityMonitoringTest|Security|Services|SimpleAuditTest|SimpleDbTest|StaffPortalRoutesTest|Staff|SubmitTicketDivisionsTest|ThemeToggleTest|ThemeToggleWidgetTest|TicketStatusBroadcastingTest|Translations|UserProfileTest|ViewRenderingTest|WebSocketConnectionTest|WelcomePageTest|WidgetCustomizationTest|WidgetPerformanceTest" >> temp_results.txt

# Replace the original file
Move-Item temp_results.txt test_results_2.txt
Choose the approach that works best for your needs. The first approach gives you the most control, while the last approach is the most efficient.
