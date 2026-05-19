<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_update_api_is_not_implemented_yet(): void
    {
        $this->markTestSkipped('Password update is not currently exposed as a backend API endpoint.');
    }
}
