@echo off
REM ICTServe MCP Server Launcher for Windows
REM This script ensures the correct working directory and PHP executable are used

cd /d "C:\XAMPP\htdocs\ictserve-031125"
php artisan mcp:start ictserve