param(
    [string]$PhpPath = "php",
    [string]$ProjectPath = "C:\XAMPP\htdocs\ictserve-031125",
    [string]$TaskName = "ICTServe Memory Sync"
)

Write-Host "Registering Windows scheduled task to run memory sync daily at 03:00..."

$action = New-ScheduledTaskAction -Execute $PhpPath -Argument "artisan memory:sync-markdown"
$trigger = New-ScheduledTaskTrigger -Daily -At 3am
Register-ScheduledTask -Action $action -Trigger $trigger -TaskName $TaskName -Description "Automatically import markdown into memory graph"

Write-Host "Scheduled task created: $TaskName"
