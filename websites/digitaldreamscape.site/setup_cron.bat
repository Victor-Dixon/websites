@echo off
REM Digital Dreamscape Cron Job Setup
REM This script sets up automatic promotion every 30 minutes

echo Setting up Digital Dreamscape automatic promotion...

REM Get the current directory
set "SCRIPT_DIR=%~dp0"

REM Create the scheduled task
schtasks /create /tn "DigitalDreamscape_Promotion" /tr "php \"%SCRIPT_DIR%auto_promotion_daemon.php\" run" /sc minute /mo 30 /rl highest /f

echo Cron job created! The system will now automatically promote artifacts every 30 minutes.
echo.
echo To view the scheduled task:
echo schtasks /query /tn "DigitalDreamscape_Promotion"
echo.
echo To delete the task if needed:
echo schtasks /delete /tn "DigitalDreamscape_Promotion"
echo.
pause