<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\OtpCode;
use App\Jobs\Mail\SendOtpEmailJob;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function send(string $email): string
    {
        $email = strtolower(trim($email));
        OtpCode::where('email', $email)->delete();
        $plain = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email'      => strtolower(trim($email)),
            'code'       => Hash::make($plain),
            'expires_at' => now()->addMinutes(10),
        ]);

        SendOtpEmailJob::dispatch($email, $plain);

        return $plain;
    }

    public function verify(string $email, string $code): bool
    {
        $email = strtolower(trim($email));

        $record = OtpCode::where('email', $email)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->get()
            ->first(fn ($r) => Hash::check($code, $r->code));

        if (! $record) {
            return false;
        }

        $record->update(['used' => true]);

        return true;
    }
}
