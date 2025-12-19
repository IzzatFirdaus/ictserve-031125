# Redis 7.4.1 Upgrade Guide for ICTServe

## Overview

This directory contains PowerShell scripts to upgrade Redis from 5.0.14.1 to 7.4.1 in your Laragon environment.

## Quick Start (Automated)

Run the complete upgrade with one command:

```powershell
.\scripts\laragon\upgrade-redis-complete.ps1
```

This will guide you through all steps with confirmations.

## Manual Step-by-Step Process

If you prefer to run each step manually:

### 1. Backup Current Data
```powershell
.\scripts\laragon\backup-redis.ps1
```

### 2. Download Redis 7.4.1
```powershell
.\scripts\laragon\download-redis-7.4.1.ps1
```

### 3. Verify Dependencies
```powershell
.\scripts\laragon\verify-redis-dependencies.ps1
```

### 4. Install Redis 7.4.1
```powershell
.\scripts\laragon\install-redis-7.4.1.ps1
```

### 5. Create Configuration
```powershell
.\scripts\laragon\create-redis-config.ps1
```
**⚠️ IMPORTANT:** Save the generated password!

### 6. Update Laragon
```powershell
.\scripts\laragon\update-laragon-ini.ps1
```

### 7. Stop Current Redis
```powershell
.\scripts\laragon\stop-redis.ps1
```

### 8. Migrate Data
```powershell
.\scripts\laragon\migrate-redis-data.ps1
```

### 9. Start Redis 7.4.1
```powershell
.\scripts\laragon\start-redis-7.4.1.ps1
```

### 10. Test Connection
```powershell
.\scripts\laragon\test-redis-connection.ps1
```

### 11. Test Laravel Integration
```powershell
.\scripts\laragon\test-laravel-redis.ps1
```

### 12. Benchmark Performance (Optional)
```powershell
.\scripts\laragon\benchmark-redis.ps1
```

## Monitoring & Maintenance

### Health Check
```powershell
.\scripts\laragon\monitor-redis.ps1
```

### Test Horizon & Reverb
```powershell
.\scripts\laragon\test-horizon-reverb.ps1
```

## Rollback

If you encounter issues, rollback to Redis 5.0.14.1:

```powershell
.\scripts\laragon\rollback-redis.ps1
```

## What Gets Upgraded

- **Redis Version**: 5.0.14.1 → 7.4.1
- **Performance**: 30-50% improvement expected
- **Security**: 4+ years of security patches
- **Features**: New Redis 7.x features (ACL, improved persistence, etc.)

## What Stays the Same

- **Data**: All existing Redis data is preserved
- **Configuration**: Optimized for ICTServe workloads
- **Integration**: Laravel, Horizon, Reverb continue to work
- **Rollback**: Redis 5.0.14.1 remains available

## Important Notes

1. **Password**: A new secure password is generated during configuration
2. **Backup**: Automatic backup is created before upgrade
3. **Downtime**: Minimal downtime (< 1 minute) during migration
4. **Testing**: All scripts include verification steps
5. **Rollback**: Full rollback capability maintained

## Configuration Files

After upgrade, these files are created/updated:

- `C:\laragon\bin\redis\redis-x64-7.4.1\redis.windows.conf` - Redis configuration
- `C:\laragon\bin\redis\redis-x64-7.4.1\redis-password.txt` - Generated password
- `C:\laragon\usr\laragon.ini` - Updated to use Redis 7.4.1
- `.env.laragon` - Updated with new Redis password

## Troubleshooting

### Redis won't start
- Check logs: `C:\laragon\data\redis\redis.log`
- Verify port 6379 is available
- Ensure Visual C++ Redistributable is installed

### Laravel can't connect
- Verify password in `.env.laragon`
- Test connection: `php artisan tinker` → `Redis::ping()`
- Check Redis is running: `Get-Process redis-server`

### Performance issues
- Run benchmark: `.\scripts\laragon\benchmark-redis.ps1`
- Check memory usage: `.\scripts\laragon\monitor-redis.ps1`
- Review configuration: `C:\laragon\bin\redis\redis-x64-7.4.1\redis.windows.conf`

### Need to rollback
- Run: `.\scripts\laragon\rollback-redis.ps1`
- All data and configuration will be restored

## Support

For issues or questions:
1. Check logs: `C:\laragon\data\redis\redis.log`
2. Review specification: `.kiro/specs/laragon-version-updates/`
3. Test with: `.\scripts\laragon\test-redis-connection.ps1`

## References

- Redis 7.4 Release Notes: https://github.com/redis/redis/releases/tag/7.4.0
- Windows Port: https://github.com/tporadowski/redis
- Laravel Redis: https://laravel.com/docs/12.x/redis
- ICTServe Documentation: `docs/D16_BROADCASTING_DOCUMENTATION.md`
