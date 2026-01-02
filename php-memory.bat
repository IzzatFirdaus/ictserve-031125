@echo off
REM ICTServe PHP Memory Helper
REM Usage: php-memory.bat [php arguments]
php -d memory_limit=512M %*