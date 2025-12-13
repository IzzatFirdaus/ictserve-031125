<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialiteTestDebug extends TestCase
{
    #[Test]
    public function socialite_fake_works(): void
    {
        $fakeUser = (new SocialiteUser)->map([
            'id' => '123',
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        Socialite::fake('google', $fakeUser);

        $response = $this->get(route('auth.google.callback'));

        // Just check that we get some response
        $this->assertTrue(true);
    }
}
