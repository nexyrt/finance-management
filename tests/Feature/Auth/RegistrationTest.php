<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_disabled(): void
    {
        // Public registration is disabled; users are created by admins only
        $response = $this->get('/register');

        $response->assertStatus(404);
    }
}
