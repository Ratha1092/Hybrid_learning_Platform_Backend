<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_web_fallback_returns_successful_response(): void
    {
        $this->get('/web')
            ->assertOk()
            ->assertSee('Web fallback active');
    }
}
