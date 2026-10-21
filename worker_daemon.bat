@echo off
title SecurePoll Email Queue Worker Daemon
echo ====================================================================
echo  SecurePoll: Automatic Email Queue & Poll Lifecycle Daemon
echo ====================================================================
echo Starting continuous background queue processor...
echo Loop Interval: 30 seconds
echo Log File: storage\logs\scheduler.log
echo Press Ctrl+C to stop the daemon at any time.
echo ====================================================================
echo.

d:\xampp\php\php.exe scheduler.php --daemon --interval=30 --verbose
pause
