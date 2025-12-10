param(
    [string]$Host = '127.0.0.1',
    [int]$Port = 8080,
    [string]$Scheme = 'http'
)

Write-Host "Starting Reverb on $Scheme://$Host:$Port"

php artisan reverb:serve --host=$Host --port=$Port --scheme=$Scheme
