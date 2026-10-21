# SecurePoll Windows Task Scheduler Teardown Script
# Removes the SecurePoll_EmailQueueWorker task

$TaskName = "SecurePoll_EmailQueueWorker"

Write-Host "====================================================================" -ForegroundColor Cyan
Write-Host " SecurePoll Windows Scheduled Task Uninstaller" -ForegroundColor Cyan
Write-Host "====================================================================" -ForegroundColor Cyan

$existing = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue

if ($existing) {
    try {
        Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
        Write-Host "[SUCCESS] Task '$TaskName' has been unregistered and removed." -ForegroundColor Green
    } catch {
        Write-Error "Failed to unregister task: $_"
        exit 1
    }
} else {
    Write-Host "[INFO] Task '$TaskName' does not exist in Task Scheduler." -ForegroundColor Yellow
}
