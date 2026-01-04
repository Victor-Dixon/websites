@echo off
REM Manual trigger for Digital Dreamscape promotion cycle

echo Running Digital Dreamscape promotion cycle...
cd /d "%~dp0"
php auto_promotion_daemon.php run

echo.
echo Promotion cycle complete!
echo.

REM Wait for user input
pause