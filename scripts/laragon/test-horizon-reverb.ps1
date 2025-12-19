# Test Horizon and Reverb with Redis 7.4.1
# Part of Redis 7.4.1 upgrade process for ICTServe

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Horizon & Reverb Integration Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Laravel Horizon
Write-Host "Test 1: Laravel Horizon Status" -ForegroundColor Cyan
try {
    $horizonStatus = php artisan horizon:status 2>&1
    Write-Host "  $horizonStatus" -ForegroundColor Gray

    if ($horizonStatus -match "running") {
        Write-Host "  ✅ Horizon is running" -ForegroundColor Green
    } elseif ($horizonStatus -match "inactive") {
        Write-Host "  ⚠️  Horizon is inactive (not started)" -ForegroundColor Yellow
        Write-Host "     Start with: php artisan horizon" -ForegroundColor Gray
    } else {
        Write-Host "  ⚠️  Horizon status unknown" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Horizon test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Queue connection
Write-Host ""
Write-Host "Test 2: Queue Connection" -ForegroundColor Cyan
$queueTest = @"
try {
    `$connection = config('queue.default');
    echo 'Queue Driver: ' . `$connection;
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$queueTest 2>&1
    Write-Host "  $result" -ForegroundColor Gray
    if ($result -match "redis") {
        Write-Host "  ✅ Queue is using Redis" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Queue is not using Redis" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Queue test failed" -ForegroundColor Red
}

# Test 3: Laravel Reverb
Write-Host ""
Write-Host "Test 3: Laravel Reverb" -ForegroundColor Cyan
Write-Host "  Starting Reverb for 5 seconds..." -ForegroundColor Gray

try {
    $reverbJob = Start-Job -ScriptBlock {
        Set-Location $using:PWD
        php artisan reverb:start 2>&1
    }

    Start-Sleep -Seconds 5

    # Check if Reverb started successfully
    $reverbOutput = Receive-Job -Job $reverbJob

    if ($reverbOutput -match "started" -or $reverbOutput -match "listening") {
        Write-Host "  ✅ Reverb started successfully" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Reverb status unclear" -ForegroundColor Yellow
    }

    Stop-Job -Job $reverbJob
    Remove-Job -Job $reverbJob
} catch {
    Write-Host "  ❌ Reverb test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Broadcasting configuration
Write-Host ""
Write-Host "Test 4: Broadcasting Configuration" -ForegroundColor Cyan
$broadcastTest = @"
try {
    `$driver = config('broadcasting.default');
    echo 'Broadcasting Driver: ' . `$driver;
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$broadcastTest 2>&1
    Write-Host "  $result" -ForegroundColor Gray
    if ($result -match "reverb") {
        Write-Host "  ✅ Broadcasting is using Reverb" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Broadcasting is not using Reverb" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ❌ Broadcasting test failed" -ForegroundColor Red
}

# Test 5: Redis connections
Write-Host ""
Write-Host "Test 5: Redis Connection Configuration" -ForegroundColor Cyan
$connectionsTest = @"
try {
    `$connections = ['default', 'cache', 'horizon', 'reverb'];
    foreach (`$connections as `$conn) {
        try {
            Redis::connection(`$conn)->ping();
            echo `$conn . ': OK' . PHP_EOL;
        } catch (Exception `$e) {
            echo `$conn . ': FAILED - ' . `$e->getMessage() . PHP_EOL;
        }
    }
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$connectionsTest 2>&1
    $lines = $result -split "`n"
    foreach ($line in $lines) {
        if ($line -match "OK") {
            Write-Host "  ✅ $line" -ForegroundColor Green
        } elseif ($line -match "FAILED") {
            Write-Host "  ❌ $line" -ForegroundColor Red
        } elseif ($line.Trim()) {
            Write-Host "  $line" -ForegroundColor Gray
        }
    }
} catch {
    Write-Host "  ❌ Connections test failed" -ForegroundColor Red
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Horizon & Reverb tests completed!" -ForegroundColor Green
Write-Host ""
Write-Host "To fully test Horizon and Reverb:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Start Horizon:" -ForegroundColor Yellow
Write-Host "   php artisan horizon" -ForegroundColor White
Write-Host ""
Write-Host "2. Start Reverb:" -ForegroundColor Yellow
Write-Host "   php artisan reverb:start" -ForegroundColor White
Write-Host ""
Write-Host "3. Dispatch a test job:" -ForegroundColor Yellow
Write-Host "   php artisan tinker" -ForegroundColor White
Write-Host "   >>> dispatch(new App\Jobs\TestJob())" -ForegroundColor White
Write-Host ""
Write-Host "4. Test broadcasting:" -ForegroundColor Yellow
Write-Host "   php artisan tinker" -ForegroundColor White
Write-Host "   >>> broadcast(new App\Events\TestEvent())" -ForegroundColor White
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
