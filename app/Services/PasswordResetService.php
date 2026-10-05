<?php

namespace App\Services;

use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    public const OTP_TTL = 300;
    public const VERIFIED_TTL = 600;
    public const MAX_ATTEMPTS = 5;

    public function sendOtp(User $user): void
    {
        $email = strtolower($user->email);
        $otp = (string) random_int(100000, 999999);

        Cache::put($this->otpKey($email), $otp, self::OTP_TTL);
        Cache::forget($this->verifiedKey($email));
        Cache::forget($this->attemptsKey($email));

        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp));
        } catch (\Throwable $exception) {
            $this->clear($email);

            throw $exception;
        }
    }

    public function verify(string $email, string $otp): string
    {
        $email = strtolower($email);
        $storedOtp = Cache::get($this->otpKey($email));

        if ($storedOtp === null) {
            return 'expired';
        }

        $attempts = (int) Cache::get($this->attemptsKey($email), 0) + 1;
        Cache::put($this->attemptsKey($email), $attempts, self::OTP_TTL);

        if (! hash_equals((string) $storedOtp, $otp)) {
            if ($attempts >= self::MAX_ATTEMPTS) {
                $this->clear($email);

                return 'locked';
            }

            return 'invalid';
        }

        Cache::forget($this->otpKey($email));
        Cache::forget($this->attemptsKey($email));
        Cache::put($this->verifiedKey($email), true, self::VERIFIED_TTL);

        return 'valid';
    }

    public function resetPassword(string $email, string $password): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($password)]);

        $this->clear($email);
    }

    public function isVerified(string $email): bool
    {
        return (bool) Cache::get($this->verifiedKey(strtolower($email)), false);
    }

    public function clear(string $email): void
    {
        $email = strtolower($email);

        Cache::forget($this->otpKey($email));
        Cache::forget($this->attemptsKey($email));
        Cache::forget($this->verifiedKey($email));
    }

    public function otpKey(string $email): string
    {
        return 'password_reset_otp_'.sha1(strtolower($email));
    }

    public function attemptsKey(string $email): string
    {
        return 'password_reset_attempts_'.sha1(strtolower($email));
    }

    public function verifiedKey(string $email): string
    {
        return 'password_reset_verified_'.sha1(strtolower($email));
    }

}
