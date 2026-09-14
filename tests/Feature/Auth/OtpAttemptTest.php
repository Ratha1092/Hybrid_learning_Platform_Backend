<?php

namespace Tests\Feature\Auth;

use App\Domains\Auth\Models\OtpCode;
use App\Domains\Auth\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OtpAttemptTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_otp_is_invalidated_after_failed_attempts(): void
    {
        Queue::fake();

        $code = OtpCode::create([
            'email' => 'new-user@example.test',
            'code' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);
        $service = app(OtpService::class);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->assertFalse($service->verify('new-user@example.test', '000000'));
        }

        $this->assertFalse($service->verify('new-user@example.test', '123456'));
        $this->assertTrue($code->fresh()->used);
        $this->assertSame(5, $code->fresh()->attempts);
    }
}