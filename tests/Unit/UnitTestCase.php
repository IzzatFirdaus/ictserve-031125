<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

/**
 * Base class for unit tests that don't require database access
 */
abstract class UnitTestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var bool Prevent automatic database seeding for all tests */
    protected $seed = false;
}
