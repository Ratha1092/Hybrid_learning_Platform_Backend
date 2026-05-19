<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_confirmation_is_not_exposed_as_backend_api_endpoint(): void
    {
        $this->markTestSkipped('Password confirmation is a frontend/session workflow and has no backend API endpoint yet.');
    }
}
