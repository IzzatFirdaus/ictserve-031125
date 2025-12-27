@echo off
REM ICTServe Test Runner for Windows
REM Runs PHPUnit tests with adequate memory allocation for Filament resource discovery
REM
REM Usage:
REM   run-tests-windows.bat                    - Run all tests
REM   run-tests-windows.bat --filter=TestName  - Run specific test
REM   run-tests-windows.bat tests/Feature/     - Run specific directory

php -d memory_limit=512M artisan test %*
