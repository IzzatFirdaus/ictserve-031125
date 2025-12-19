# Laragon Version Updates - Design Document

## Architecture Overview

This design outlines the upgrade strategy for Laragon components, focusing on Redis 7.4.1 as the critical update for ICTServe v3.6.0 production readiness.

## Component Architecture

### Redis 7.4.1 Integration

```
┌─────────────────────────────────────────────────────────────┐
│                    Laragon Service Manager                   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│              Redis 7.4.1 (Windows Port)                      │
│  Location: C:\laragon\bin\redis\redis-x64-7.4.1             │
│  Config: redis.windows.conf                                  │
│  Data: C:\laragon\data\redis                                 │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┼─────────────┐
                ▼             ▼             ▼
        ┌──────────┐  ┌──────────┐  ┌──────────┐
        │  Cache   │  │  Queue   │  │ Session  │
        │  Layer   │  │ (Horizon)│  │  Store   │
        └──────────┘  └──────────┘  └──────────┘
                              │
                              ▼
        ┌─────────────────────────────────────────┐
        │      ICTServe Laravel 12 Application     │
        │  - Reverb (WebSocket)                    │
        │  - Horizon (Queue Management)            │
        │  - Cache (Application Cache)             │
        │  - Session (User Sessions)               │
        └─────────────────────────────────────────┘
```

## Redis 7.4.1 Configuration Design

### Directory Structure

```
C:\laragon\bin\redis\
├── redis-x64-5.0.14.1\          # Old version (backup)
│   ├── redis-server.exe
│   ├── redis-cli.exe
│   └── redis.windows.conf
└── redis-x64-7.4.1\             # New version
    ├── redis-server.exe
    ├── redis-cli.exe
    ├── redis-benchmark.exe
    ├── redis-check-aof.exe
    ├── redis-check-rdb.exe
    ├── redis.windows.conf       # Optimized config
    └── redis-acl.conf           # ACL configuration (new in 6.x+)
```

### Configuration File Design

**File**: `C:\laragon\bin\redis\redis-x64-7.4.1\redis.windows.conf`

```ini
# Redis 7.4.1 Configuration for ICTServe Production
# Optimized for Laravel Reverb + Horizon + Cache

# Network Configuration
bind 127.0.0.1
port 6379
tcp-backlog 511
timeout 0
tcp-keepalive 300

# General Configuration
daemonize no
supervised no
pidfile /var/run/redis_6379.pid
loglevel notice
logfile "C:/laragon/data/redis/redis.log"
databases 16

# Snapshotting (RDB Persistence)
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir "C:/laragon/data/redis/"

# Replication (for future scaling)
replica-serve-stale-data yes
replica-read-only yes
repl-diskless-sync no
repl-diskless-sync-delay 5

# Security
requirepass your_secure_password_here
# ACL configuration file
aclfile "C:/laragon/bin/redis/redis-x64-7.4.1/redis-acl.conf"

# Memory Management (CRITICAL for ICTServe)
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Lazy Freeing (Performance Optimization)
lazyfree-lazy-eviction yes
lazyfree-lazy-expire yes
lazyfree-lazy-server-del yes
replica-lazy-flush yes

# Append Only File (AOF Persistence)
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb
aof-load-truncated yes
aof-use-rdb-preamble yes

# Lua Scripting
lua-time-limit 5000

# Slow Log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Latency Monitor
latency-monitor-threshold 100

# Event Notification (for Laravel Broadcasting)
notify-keyspace-events Ex

# Advanced Configuration
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
list-compress-depth 0
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64
hll-sparse-max-bytes 3000
stream-node-max-bytes 4096
stream-node-max-entries 100
activerehashing yes
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60
hz 10
dynamic-hz yes
aof-rewrite-incremental-fsync yes
rdb-save-incremental-fsync yes

# Windows Specific
# heapdir "C:/laragon/data/redis/"
```

### ACL Configuration Design

**File**: `C:\laragon\bin\redis\redis-x64-7.4.1\redis-acl.conf`

```
# Redis ACL Configuration for ICTServe
# Defines user permissions for different Laravel services

# Default user (disabled for security)
user default off

# Laravel application user (full access)
user laravel on >your_laravel_password ~* &* +@all

# Horizon queue user (limited to queue operations)
user horizon on >your_horizon_password ~queues:* ~horizon:* +@read +@write +@list +@set +@sortedset

# Reverb WebSocket user (limited to pub/sub)
user reverb on >your_reverb_password ~reverb:* +@pubsub +@read +@write

# Cache user (limited to cache operations)
user cache on >your_cache_password ~cache:* +@read +@write +@string +@hash

# Read-only monitoring user
user monitor on >your_monitor_password ~* +@read +@dangerous
```

## Laravel Configuration Updates

### Database Configuration

**File**: `config/database.php`

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME', 'laravel'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'read_timeout' => 60,
        'context' => [
            'stream' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ],
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_CACHE_USERNAME', 'cache'),
        'password' => env('REDIS_CACHE_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],

    'horizon' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_HORIZON_USERNAME', 'horizon'),
        'password' => env('REDIS_HORIZON_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_HORIZON_DB', '2'),
    ],

    'reverb' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_REVERB_USERNAME', 'reverb'),
        'password' => env('REDIS_REVERB_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_REVERB_DB', '3'),
    ],
],
```

### Environment Configuration

**File**: `.env.laragon` (updated)

```env
# Redis 7.4.1 Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Main Laravel connection
REDIS_USERNAME=laravel
REDIS_PASSWORD=your_laravel_password
REDIS_DB=0

# Cache connection
REDIS_CACHE_USERNAME=cache
REDIS_CACHE_PASSWORD=your_cache_password
REDIS_CACHE_DB=1

# Horizon connection
REDIS_HORIZON_USERNAME=horizon
REDIS_HORIZON_PASSWORD=your_horizon_password
REDIS_HORIZON_DB=2

# Reverb connection
REDIS_REVERB_USERNAME=reverb
REDIS_REVERB_PASSWORD=your_reverb_password
REDIS_REVERB_DB=3

# Redis Prefix
REDIS_PREFIX=ictserve_
```

## Migration Strategy

### Phase 1: Preparation (Pre-Migration)

1. **Backup Current Redis Data**
   ```powershell
   # Backup script
   $backupDir = "C:\laragon\data\redis\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
   New-Item -ItemType Directory -Path $backupDir
   Copy-Item "C:\laragon\data\redis\dump.rdb" -Destination $backupDir
   Copy-Item "C:\laragon\data\redis\appendonly.aof" -Destination $backupDir -ErrorAction SilentlyContinue
   ```

2. **Download Redis 7.4.1**
   - URL: https://github.com/tporadowski/redis/releases/download/v7.4.1/Redis-x64-7.4.1.zip
   - Extract to: `C:\laragon\bin\redis\redis-x64-7.4.1`

3. **Verify Dependencies**
   - Visual C++ Redistributable 2015-2022 installed
   - Windows Firewall rules configured

### Phase 2: Configuration (Setup)

1. **Create Optimized Configuration**
   - Copy template configuration to `redis.windows.conf`
   - Update paths for Windows environment
   - Set secure passwords for ACL users

2. **Create ACL Configuration**
   - Define user roles (laravel, horizon, reverb, cache)
   - Set appropriate permissions per user
   - Generate secure passwords

3. **Update Laragon Configuration**
   ```ini
   # C:\laragon\usr\laragon.ini
   [redis]
   Version=redis-x64-7.4.1
   Use=-1
   ```

### Phase 3: Migration (Execution)

1. **Stop Current Redis**
   ```powershell
   # Stop Redis 5.0.14.1
   Stop-Process -Name "redis-server" -Force -ErrorAction SilentlyContinue
   ```

2. **Migrate Data**
   ```powershell
   # Copy RDB file to new location
   Copy-Item "C:\laragon\data\redis\dump.rdb" -Destination "C:\laragon\data\redis\dump.rdb.new"
   ```

3. **Start Redis 7.4.1**
   ```powershell
   # Start new Redis version
   & "C:\laragon\bin\redis\redis-x64-7.4.1\redis-server.exe" "C:\laragon\bin\redis\redis-x64-7.4.1\redis.windows.conf"
   ```

4. **Verify Migration**
   ```powershell
   # Test connection
   & "C:\laragon\bin\redis\redis-x64-7.4.1\redis-cli.exe" -a your_laravel_password ping
   # Expected: PONG
   
   # Check data
   & "C:\laragon\bin\redis\redis-x64-7.4.1\redis-cli.exe" -a your_laravel_password dbsize
   # Should show existing key count
   ```

### Phase 4: Validation (Testing)

1. **Laravel Connection Test**
   ```bash
   php artisan tinker
   >>> Redis::ping()
   # Expected: "PONG"
   
   >>> Redis::set('test_key', 'test_value')
   >>> Redis::get('test_key')
   # Expected: "test_value"
   ```

2. **Horizon Test**
   ```bash
   php artisan horizon:status
   # Should show running status
   ```

3. **Reverb Test**
   ```bash
   php artisan reverb:start --debug
   # Should connect to Redis successfully
   ```

4. **Performance Benchmark**
   ```powershell
   # Run benchmark
   & "C:\laragon\bin\redis\redis-x64-7.4.1\redis-benchmark.exe" -a your_laravel_password -q -n 100000
   ```

### Phase 5: Rollback (If Needed)

```powershell
# Rollback script
Stop-Process -Name "redis-server" -Force
Copy-Item "C:\laragon\data\redis\backup_*\dump.rdb" -Destination "C:\laragon\data\redis\dump.rdb"

# Update laragon.ini
# [redis]
# Version=redis-x64-5.0.14.1

# Restart old version
& "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe"
```

## Performance Optimization

### Memory Management

- **Maxmemory**: 512MB (adjust based on available RAM)
- **Eviction Policy**: `allkeys-lru` (Least Recently Used)
- **Lazy Freeing**: Enabled for better performance

### Persistence Strategy

- **RDB**: Snapshots every 15 minutes (if 1+ keys changed)
- **AOF**: Append-only file with `everysec` fsync
- **Hybrid**: RDB preamble in AOF for faster restarts

### Connection Pooling

- **Max Clients**: 10,000 (default)
- **Timeout**: 0 (no timeout for persistent connections)
- **TCP Keepalive**: 300 seconds

## Security Considerations

### Authentication

- **ACL**: Role-based access control for different services
- **Password**: Strong passwords for each user role
- **Bind**: Localhost only (127.0.0.1)

### Data Protection

- **Encryption**: TLS for production (future enhancement)
- **Backup**: Automated daily backups
- **Monitoring**: Slow log and latency monitoring enabled

## Monitoring & Maintenance

### Health Checks

```powershell
# Redis health check script
function Test-RedisHealth {
    $redis = & "C:\laragon\bin\redis\redis-x64-7.4.1\redis-cli.exe" -a your_laravel_password ping
    if ($redis -eq "PONG") {
        Write-Host "✅ Redis is healthy" -ForegroundColor Green
        return $true
    } else {
        Write-Host "❌ Redis is not responding" -ForegroundColor Red
        return $false
    }
}
```

### Performance Monitoring

```bash
# Laravel command to monitor Redis
php artisan redis:monitor

# Redis CLI monitoring
redis-cli -a your_laravel_password --latency
redis-cli -a your_laravel_password --stat
```

### Maintenance Tasks

1. **Daily**: Check slow log for performance issues
2. **Weekly**: Review memory usage and eviction stats
3. **Monthly**: Analyze AOF file size and rewrite if needed
4. **Quarterly**: Review ACL permissions and update as needed

## Integration Points

### Laravel Reverb

- Uses `reverb` Redis connection
- Pub/Sub for WebSocket broadcasting
- Requires `notify-keyspace-events Ex` enabled

### Laravel Horizon

- Uses `horizon` Redis connection
- Queue management and monitoring
- Requires sorted set and list operations

### Application Cache

- Uses `cache` Redis connection
- LRU eviction policy
- Separate database for isolation

### Session Storage

- Uses `default` Redis connection
- Persistent sessions across requests
- Automatic expiration handling

## Testing Strategy

### Unit Tests

```php
// tests/Unit/RedisConnectionTest.php
public function test_redis_connection(): void
{
    $this->assertTrue(Redis::ping());
}

public function test_redis_acl_permissions(): void
{
    // Test each user role has appropriate permissions
    $this->assertTrue(Redis::connection('cache')->set('test', 'value'));
    $this->assertEquals('value', Redis::connection('cache')->get('test'));
}
```

### Integration Tests

```php
// tests/Feature/RedisIntegrationTest.php
public function test_horizon_uses_redis(): void
{
    dispatch(new TestJob());
    $this->assertTrue(Redis::connection('horizon')->exists('horizon:*'));
}

public function test_reverb_broadcasting(): void
{
    broadcast(new TestEvent());
    // Verify event published to Redis pub/sub
}
```

### Performance Tests

```bash
# Benchmark script
php artisan redis:benchmark --operations=100000
```

## Documentation Updates

### Files to Update

1. `README.md` - Update Redis version
2. `XAMPP_SETUP_README.md` - Add Redis 7.x notes
3. `.kiro/steering/tech.md` - Update technology stack
4. `scripts/laragon/setup-laragon.ps1` - Update Redis installation
5. `scripts/laragon/redis-health-check.ps1` - Update for Redis 7.x

## Success Criteria

1. ✅ Redis 7.4.1 running in Laragon
2. ✅ All Laravel connections working (default, cache, horizon, reverb)
3. ✅ ACL configured with role-based access
4. ✅ Performance improved by 30%+ over Redis 5.0.14.1
5. ✅ Zero data loss during migration
6. ✅ All tests passing
7. ✅ Documentation updated

## References

- Redis 7.4 Documentation: https://redis.io/docs/
- Windows Port: https://github.com/tporadowski/redis
- Laravel Redis: https://laravel.com/docs/12.x/redis
- ICTServe D16: Broadcasting with Redis
