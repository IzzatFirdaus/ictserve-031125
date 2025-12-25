@echo off
echo ICTServe Database Backup
echo ========================
echo.

cd /d "C:\XAMPP\htdocs\ictserve-031125"

echo Creating database backup...
php artisan db:backup

echo.
echo Backup completed!
echo Location: storage\app\backups\
echo.
pause