<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DatabaseHostFallbackTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    #[Test]
    public function it_falls_back_from_docker_db_host_name_when_it_does_not_resolve(): void
    {
        $basePath = dirname(__DIR__, 2);

        putenv('APP_ENV=testing');
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';

        putenv('DB_HOST=db');
        $_ENV['DB_HOST'] = 'db';

        $resolved = gethostbyname('db');

        if ($resolved !== 'db') {
            $this->markTestSkipped('Host "db" resolves in this environment; fallback is not expected.');
        }

        require $basePath.'/bootstrap/app.php';

        $databaseConfig = require $basePath.'/config/database.php';

        $this->assertSame('127.0.0.1', $databaseConfig['connections']['mysql']['host']);
        $this->assertSame('127.0.0.1', $databaseConfig['connections']['mariadb']['host']);
    }
}
