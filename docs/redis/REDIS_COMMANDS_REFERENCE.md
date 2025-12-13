# Redis Commands Quick Reference

**Version**: Redis 7.0.15  
**Environment**: WSL Ubuntu 24.04 LTS  
**Purpose**: Quick command reference for ICTServe developers  
**Last Updated**: December 7, 2025

---

## Table of Contents

1. [Connection & Server](#connection--server)
2. [Key Management](#key-management)
3. [String Operations](#string-operations)
4. [List Operations](#list-operations)
5. [Hash Operations](#hash-operations)
6. [Set Operations](#set-operations)
7. [Sorted Set Operations](#sorted-set-operations)
8. [Pub/Sub](#pubsub)
9. [Transactions](#transactions)
10. [Laravel-Specific Keys](#laravel-specific-keys)
11. [Performance & Monitoring](#performance--monitoring)
12. [Backup & Maintenance](#backup--maintenance)

---

## Connection & Server

### Connect to Redis

```bash
# From WSL
redis-cli

# From Windows PowerShell
wsl.exe -e redis-cli

# With password
redis-cli -a your_password

# Specific host and port
redis-cli -h 127.0.0.1 -p 6379

# Select database (0-15)
redis-cli -n 1
```

### Server Information

```redis
PING                          # Test connection (returns PONG)
INFO                          # Full server info
INFO server                   # Server version, OS, uptime
INFO memory                   # Memory usage stats
INFO stats                    # Commands processed, hits/misses
INFO clients                  # Connected clients
INFO replication              # Master/slave status
CONFIG GET *                  # All configuration values
CONFIG GET maxmemory          # Specific config value
CONFIG SET maxmemory 512mb    # Set config value
CLIENT LIST                   # List connected clients
DBSIZE                        # Number of keys in current DB
```

---

## Key Management

### Basic Key Operations

```redis
KEYS *                        # List all keys (⚠️ slow on large DBs)
KEYS cache:*                  # List keys matching pattern
SCAN 0 MATCH cache:* COUNT 100  # Iterator-based key scan (preferred)

EXISTS key                    # Check if key exists (1/0)
TYPE key                      # Get key type (string/list/hash/set/zset)
TTL key                       # Time to live in seconds (-1 = no expire, -2 = not exist)
PTTL key                      # Time to live in milliseconds

EXPIRE key 3600               # Set expiration (seconds)
EXPIREAT key 1733598000       # Set expiration (Unix timestamp)
PERSIST key                   # Remove expiration
RENAME oldkey newkey          # Rename key
DEL key                       # Delete key
DEL key1 key2 key3            # Delete multiple keys

RANDOMKEY                     # Get random key
DUMP key                      # Serialize key value
```

### Database Management

```redis
SELECT 0                      # Switch to database 0 (0-15)
FLUSHDB                       # Delete all keys in current DB
FLUSHALL                      # Delete all keys in all DBs
MOVE key 1                    # Move key to database 1
SWAPDB 0 1                    # Swap database 0 with database 1
```

---

## String Operations

### Set & Get

```redis
SET key value                 # Set string value
GET key                       # Get string value
MSET key1 val1 key2 val2      # Set multiple keys
MGET key1 key2 key3           # Get multiple keys

SETNX key value               # Set if not exists (1=set, 0=exists)
SETEX key 60 value            # Set with expiration (seconds)
PSETEX key 60000 value        # Set with expiration (milliseconds)

APPEND key value              # Append to existing value
STRLEN key                    # Get string length
GETRANGE key 0 10             # Get substring
SETRANGE key 5 "new"          # Replace part of string
```

### Increment & Decrement

```redis
INCR counter                  # Increment by 1
DECR counter                  # Decrement by 1
INCRBY counter 5              # Increment by 5
DECRBY counter 5              # Decrement by 5
INCRBYFLOAT price 0.5         # Increment by float
```

---

## List Operations

### Add Elements

```redis
LPUSH list value              # Prepend to list
RPUSH list value              # Append to list
LPUSH list val1 val2 val3     # Prepend multiple
RPUSH list val1 val2 val3     # Append multiple

LPUSHX list value             # Prepend only if list exists
RPUSHX list value             # Append only if list exists

LINSERT list BEFORE pivot val # Insert before element
LINSERT list AFTER pivot val  # Insert after element
```

### Get Elements

```redis
LRANGE list 0 -1              # Get all elements
LRANGE list 0 9               # Get first 10 elements
LINDEX list 0                 # Get element at index
LLEN list                     # Get list length
```

### Remove Elements

```redis
LPOP list                     # Remove and return first element
RPOP list                     # Remove and return last element
LREM list 0 value             # Remove all occurrences
LREM list 2 value             # Remove first 2 occurrences
LTRIM list 0 99               # Keep only first 100 elements
```

### Blocking Operations

```redis
BLPOP list 5                  # Block pop left (5 sec timeout)
BRPOP list 5                  # Block pop right (5 sec timeout)
BRPOPLPUSH src dest 5         # Block pop from src, push to dest
```

---

## Hash Operations

### Set & Get

```redis
HSET hash field value         # Set hash field
HGET hash field               # Get hash field
HMSET hash f1 v1 f2 v2        # Set multiple fields
HMGET hash f1 f2 f3           # Get multiple fields
HGETALL hash                  # Get all fields and values

HSETNX hash field value       # Set if field doesn't exist
HDEL hash field               # Delete hash field
HDEL hash f1 f2 f3            # Delete multiple fields
```

### Hash Information

```redis
HEXISTS hash field            # Check if field exists
HLEN hash                     # Number of fields
HKEYS hash                    # Get all field names
HVALS hash                    # Get all values
HSTRLEN hash field            # Get field value length
```

### Increment

```redis
HINCRBY hash field 5          # Increment field by 5
HINCRBYFLOAT hash field 0.5   # Increment field by float
```

---

## Set Operations

### Add & Remove

```redis
SADD set member               # Add member to set
SADD set m1 m2 m3             # Add multiple members
SREM set member               # Remove member
SREM set m1 m2 m3             # Remove multiple members
SPOP set                      # Remove and return random member
SPOP set 3                    # Remove and return 3 random members
```

### Query Set

```redis
SMEMBERS set                  # Get all members
SCARD set                     # Get set size
SISMEMBER set member          # Check if member exists
SRANDMEMBER set               # Get random member (without removing)
SRANDMEMBER set 3             # Get 3 random members
```

### Set Operations

```redis
SUNION set1 set2              # Union of sets
SINTER set1 set2              # Intersection of sets
SDIFF set1 set2               # Difference (set1 - set2)

SUNIONSTORE dest set1 set2    # Store union result
SINTERSTORE dest set1 set2    # Store intersection result
SDIFFSTORE dest set1 set2     # Store difference result

SMOVE src dest member         # Move member between sets
```

---

## Sorted Set Operations

### Add & Update

```redis
ZADD zset 1 member            # Add member with score 1
ZADD zset 1 m1 2 m2 3 m3      # Add multiple members
ZINCRBY zset 5 member         # Increment member score by 5
```

### Query by Rank

```redis
ZRANGE zset 0 -1              # Get all members (by rank, ascending)
ZRANGE zset 0 9               # Get first 10 members
ZRANGE zset 0 -1 WITHSCORES   # Include scores
ZREVRANGE zset 0 -1           # Reverse order (descending)

ZRANK zset member             # Get rank (0-based)
ZREVRANK zset member          # Get reverse rank
```

### Query by Score

```redis
ZRANGEBYSCORE zset 0 100      # Get members with score 0-100
ZRANGEBYSCORE zset -inf +inf  # All members by score
ZRANGEBYSCORE zset 0 100 WITHSCORES LIMIT 0 10  # First 10 with scores

ZREVRANGEBYSCORE zset 100 0   # Reverse order
ZCOUNT zset 0 100             # Count members with score 0-100
```

### Get Score

```redis
ZSCORE zset member            # Get member score
ZCARD zset                    # Get sorted set size
```

### Remove

```redis
ZREM zset member              # Remove member
ZREM zset m1 m2 m3            # Remove multiple members
ZREMRANGEBYRANK zset 0 9      # Remove first 10 by rank
ZREMRANGEBYSCORE zset 0 100   # Remove by score range
```

---

## Pub/Sub

### Publish & Subscribe

```redis
PUBLISH channel message       # Publish message to channel
SUBSCRIBE channel             # Subscribe to channel
PSUBSCRIBE chan*              # Subscribe to pattern
UNSUBSCRIBE channel           # Unsubscribe from channel
PUNSUBSCRIBE chan*            # Unsubscribe from pattern

PUBSUB CHANNELS               # List active channels
PUBSUB NUMSUB channel         # Number of subscribers
PUBSUB NUMPAT                 # Number of pattern subscriptions
```

---

## Transactions

### MULTI/EXEC

```redis
MULTI                         # Start transaction
SET key value
INCR counter
EXPIRE key 60
EXEC                          # Execute transaction

DISCARD                       # Cancel transaction
```

### Watch (Optimistic Locking)

```redis
WATCH key                     # Watch key for changes
MULTI
SET key new_value
EXEC                          # Fails if key changed since WATCH

UNWATCH                       # Cancel all watches
```

---

## Laravel-Specific Keys

### Cache Keys

```redis
# Laravel cache format: laravel_database_{key}
KEYS laravel_database_*       # List all Laravel cache keys
GET laravel_database_cache_key  # Get specific cache value
DEL laravel_database_cache_key  # Delete cache key

# Cache tags (if used)
KEYS laravel_database_tag:*
```

### Session Keys

```redis
# Laravel session format: laravel_database_session:{session_id}
KEYS laravel_database_session:*  # List all sessions
GET laravel_database_session:abc123def456  # Get session data
TTL laravel_database_session:abc123def456  # Session expiration
```

### Queue Keys

```redis
# Laravel queue format
LLEN laravel_database_queues:default  # Queue length
LRANGE laravel_database_queues:default 0 -1  # View queued jobs
LPUSH laravel_database_queues:default '{"job":"..."}'  # Add job
```

### Broadcast Keys

```redis
# Laravel Reverb/Broadcasting
KEYS laravel_database_private:*
KEYS laravel_database_presence:*
```

### Common Laravel Commands

```bash
# From Laravel application
php artisan cache:clear        # Clear all cache
php artisan cache:forget key   # Delete specific key
php artisan config:cache       # Cache configuration
php artisan route:cache        # Cache routes
php artisan view:cache         # Cache views

# Via Tinker
php artisan tinker
>>> cache()->put('test', 'value', 600);
>>> cache()->get('test');
>>> cache()->forget('test');
>>> cache()->flush();
```

---

## Performance & Monitoring

### Monitor Commands

```redis
MONITOR                       # Watch all incoming commands (verbose)
SLOWLOG GET 10                # Get last 10 slow commands
SLOWLOG LEN                   # Number of slow commands logged
SLOWLOG RESET                 # Clear slow log

CLIENT LIST                   # List connected clients
CLIENT KILL ip:port           # Disconnect client
CLIENT SETNAME connection1    # Name current connection
CLIENT GETNAME                # Get connection name
```

### Statistics

```redis
INFO stats                    # Commands processed, hits/misses
INFO commandstats             # Per-command statistics
INFO keyspace                 # Keys per database

# Real-time stats from shell
redis-cli --stat              # Commands/sec, hits/misses, memory
redis-cli --stat -i 2         # Update every 2 seconds
```

### Memory Analysis

```redis
INFO memory                   # Memory usage overview
MEMORY USAGE key              # Memory used by specific key
MEMORY DOCTOR                 # Memory usage report
MEMORY STATS                  # Detailed memory stats

MEMORY PURGE                  # Free memory from allocator
```

### Benchmarking

```bash
# From shell
redis-benchmark               # Run default benchmark
redis-benchmark -t set,get -n 100000  # Benchmark SET/GET
redis-benchmark -q            # Quiet mode (only summary)
redis-benchmark -c 50         # 50 parallel clients
redis-benchmark -d 1024       # 1KB value size
```

---

## Backup & Maintenance

### Save Database

```redis
SAVE                          # Synchronous save (blocks server)
BGSAVE                        # Background save (non-blocking)
LASTSAVE                      # Unix timestamp of last successful save

# Configuration
CONFIG GET save               # Show save frequency
CONFIG SET save "900 1 300 10"  # Save after 900s if 1 key changed
```

### Append-Only File (AOF)

```redis
BGREWRITEAOF                  # Rewrite AOF file (optimize size)

# Configuration
CONFIG GET appendonly         # Check if AOF enabled
CONFIG GET appendfsync        # Sync frequency (always/everysec/no)
```

### Database Management

```redis
FLUSHDB                       # Delete all keys in current DB
FLUSHALL                      # Delete all keys in all DBs
FLUSHDB ASYNC                 # Async delete (non-blocking)
FLUSHALL ASYNC                # Async delete all (non-blocking)
```

### Shutdown

```redis
SHUTDOWN                      # Stop server (save DB first)
SHUTDOWN NOSAVE               # Stop without saving
SHUTDOWN SAVE                 # Stop and force save
```

---

## Advanced Commands

### Key Expiration

```redis
EXPIRE key 60                 # Expire in 60 seconds
EXPIREAT key 1733598000       # Expire at Unix timestamp
PEXPIRE key 60000             # Expire in 60000 milliseconds
PERSIST key                   # Remove expiration

# Check expiration
TTL key                       # Seconds remaining (-1=no expire, -2=not exist)
PTTL key                      # Milliseconds remaining
```

### Bit Operations

```redis
SETBIT key 0 1                # Set bit at offset 0
GETBIT key 0                  # Get bit at offset 0
BITCOUNT key                  # Count set bits
BITOP AND dest key1 key2      # Bitwise AND
BITOP OR dest key1 key2       # Bitwise OR
BITOP XOR dest key1 key2      # Bitwise XOR
```

### HyperLogLog (Cardinality Estimation)

```redis
PFADD hll element             # Add element
PFCOUNT hll                   # Get cardinality estimate
PFMERGE dest hll1 hll2        # Merge HyperLogLogs
```

---

## Useful Patterns

### Rate Limiting

```redis
# Simple rate limit (10 requests per minute)
INCR user:123:requests
EXPIRE user:123:requests 60
GET user:123:requests         # Check if > 10

# Token bucket
SET user:123:tokens 10 NX
DECR user:123:tokens
GET user:123:tokens           # Check if >= 0
```

### Distributed Locking

```redis
# Acquire lock
SET lock:resource_id unique_token NX EX 30  # 30 sec expiration
# Returns OK if acquired, nil if locked

# Release lock (use Lua script to ensure atomicity)
EVAL "if redis.call('get',KEYS[1]) == ARGV[1] then return redis.call('del',KEYS[1]) else return 0 end" 1 lock:resource_id unique_token
```

### Leaderboard

```redis
# Add player scores
ZADD leaderboard 100 player1
ZADD leaderboard 150 player2

# Get top 10
ZREVRANGE leaderboard 0 9 WITHSCORES

# Get player rank
ZREVRANK leaderboard player1

# Increment score
ZINCRBY leaderboard 5 player1
```

### Session Storage

```redis
# Create session
HMSET session:abc123 user_id 42 email user@example.com logged_in true
EXPIRE session:abc123 3600

# Get session data
HGETALL session:abc123

# Update field
HSET session:abc123 last_seen 1733598000

# Delete session
DEL session:abc123
```

---

## Debugging Commands

### Debug Keys

```redis
OBJECT ENCODING key           # Internal encoding (e.g., ziplist, hashtable)
OBJECT REFCOUNT key           # Reference count
OBJECT IDLETIME key           # Seconds since last access
DEBUG OBJECT key              # Full debug info
```

### Server Debug

```redis
DEBUG RELOAD                  # Reload DB from disk
DEBUG SEGFAULT                # Crash server (for testing)
ROLE                          # Master/slave role
TIME                          # Server time
COMMAND COUNT                 # Number of commands
COMMAND INFO GET              # Info about GET command
```

---

## Resources

- **Redis Commands Documentation**: <https://redis.io/commands>
- **Redis CLI Tutorial**: <https://redis.io/topics/rediscli>
- **Laravel Redis Docs**: <https://laravel.com/docs/12.x/redis>
- **Redis Best Practices**: <https://redis.io/topics/lru-cache>
- **ICTServe Redis Setup**: [redis-setup.md](redis-setup.md)
- **ICTServe WSL Setup**: [WSL_SETUP.md](WSL_SETUP.md)
- **phpRedisAdmin Guide**: [PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md)

---

## Quick Reference Card

```
Connection:         redis-cli, PING, AUTH, SELECT, QUIT
Keys:               KEYS, SCAN, EXISTS, DEL, EXPIRE, TTL, TYPE
Strings:            SET, GET, MSET, MGET, INCR, DECR, APPEND
Lists:              LPUSH, RPUSH, LPOP, RPOP, LRANGE, LLEN
Hashes:             HSET, HGET, HMSET, HGETALL, HDEL, HINCRBY
Sets:               SADD, SREM, SMEMBERS, SINTER, SUNION, SDIFF
Sorted Sets:        ZADD, ZRANGE, ZRANK, ZSCORE, ZINCRBY
Pub/Sub:            PUBLISH, SUBSCRIBE, PSUBSCRIBE
Transactions:       MULTI, EXEC, DISCARD, WATCH
Server:             INFO, CONFIG, SAVE, BGSAVE, FLUSHDB, SHUTDOWN
Monitoring:         MONITOR, SLOWLOG, CLIENT LIST, MEMORY USAGE
```

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team  
**Status**: ✅ Complete Reference for Redis 7.0.15
