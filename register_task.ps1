# SecurePoll Windows Task Scheduler Setup Script
# Registers a scheduled task to run scheduler.php every 5 minutes

$TaskName = "SecurePoll_EmailQueueWorker"
$PhpExe = "d:\xampp\php\php.exe"
$ScriptPath = "d:\xampp\htdocs\SecurePoll\scheduler.php"

if (-not (Test-Path $PhpExe)) {
    Write-Error "PHP executable not found at $PhpExe. Please check your XAMPP installation."
    exit 1
}

if (-not (Test-Path $ScriptPath)) {
    Write-Error "Scheduler script not found at $ScriptPath."
    exit 1
}

Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host " SecurePoll Windows Scheduled Task Installer" -ForegroundColor Cyan
Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host "Task Name:        $TaskName"
Write-Host "PHP Path:         $PhpExe"
Write-Host "Scheduler Script: $ScriptPath"
Write-Host "Execution Cadence: Every 5 minutes (indefinitely)"
Write-Host ""

# Define Task Action
$Action = New-ScheduledTaskAction -Execute $PhpExe -Argument "-f `"$ScriptPath`"" -WorkingDirectory "d:\xampp\htdocs\SecurePoll"

# Define Trigger (Runs daily, repeated every 5 minutes for 1 day, repeated daily)
$Trigger = New-ScheduledTaskTrigger -Daily -At "00:00"
$Trigger.Repetition = (New-ScheduledTaskTrigger -Once -At "00:00" -RepetitionInterval (New-TimeSpan -Minutes 5)).Repetition

# Define Settings (Ignore new if already running; start even if on battery)
$Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -MultipleInstances IgnoreNew -StartWhenAvailable

# Register or Update Task
try {
    Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Settings $Settings -Description "Runs SecurePoll email queue worker and poll lifecycle scheduler every 5 minutes" -Force | Out-Null
    Write-Host "[SUCCESS] Task '$TaskName' successfully registered!" -ForegroundColor Green
    Write-Host ""
    Write-Host "To verify execution:" -ForegroundColor Yellow
    Write-Host "  Get-ScheduledTask -TaskName '$TaskName'" -ForegroundColor Yellow
    Write-Host "  Get-Content storage\logs\scheduler.log -Tail 20" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "To remove task when needed, run:" -ForegroundColor Yellow
    Write-Host "  .\unregister_task.ps1" -ForegroundColor Yellow
} catch {
    Write-Error "Failed to register scheduled task: $_"
    exit 1
}
