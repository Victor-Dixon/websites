@echo off
REM Automated Episode Deployment Script
REM This script runs the full episode deployment pipeline

echo 🚀 Starting Automated Episode Deployment...
echo ==========================================

REM Change to the websites directory
cd /d D:\websites

REM Run the automated deployment script
php automated_episode_deployment.php

echo.
echo 🎉 Deployment process complete!
echo Press any key to continue...
pause > nul