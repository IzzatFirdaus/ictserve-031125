<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RedisFallbackConfigTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    #[Test]
    public function it_falls_back_from_redis_when_no_redis_client_is_available(): void
    {
        $basePath = dirname(__DIR__, 2);

        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        putenv('CACHE_STORE=redis');
        $_ENV['CACHE_STORE'] = 'redis';

        putenv('SESSION_DRIVER=redis');
        $_ENV['SESSION_DRIVER'] = 'redis';

        putenv('QUEUE_CONNECTION=redis');
        $_ENV['QUEUE_CONNECTION'] = 'redis';

        require $basePath.'/bootstrap/app.php';

        $extRedisLoaded = extension_loaded('redis');
        $predisAvailable = class_exists(\Predis\Client::class);
        $redisClientAvailable = $extRedisLoaded || $predisAvailable;

        $expectedCache = $redisClientAvailable ? 'redis' : 'file';
        $expectedSession = $redisClientAvailable ? 'redis' : 'file';
        $expectedQueue = $redisClientAvailable ? 'redis' : 'sync';

        $cacheConfig = require $basePath.'/config/cache.php';
        $sessionConfig = require $basePath.'/config/session.php';
        $queueConfig = require $basePath.'/config/queue.php';

        $debug = 'ext_redis='.(int) $extRedisLoaded.' predis='.(int) $predisAvailable;

        $this->assertSame($expectedCache, $cacheConfig['default'], $debug.' expected_cache='.$expectedCache);
        $this->assertSame($expectedSession, $sessionConfig['driver'], $debug.' expected_session='.$expectedSession);
        $this->assertSame($expectedQueue, $queueConfig['default'], $debug.' expected_queue='.$expectedQueue);
    }
}
