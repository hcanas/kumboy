<?php
namespace App\Services;

use App\Models\VerificationCode;
use Illuminate\Database\QueryException;

class VerificationService
{
    public function generateCode($email)
    {
        try {
            $verificationCode = [
                'email' => $email,
                'code' => bin2hex(random_bytes(4)),
                'created_at' => now(),
                'expires_at' => now()->addMinutes(15),
                'status' => 'unused',
            ];

            VerificationCode::query()
                ->create($verificationCode);

            return $verificationCode['code'];
        } catch (QueryException | \Exception $e) {
            logger($e);
            return null;
        }
    }

    public function consumeVerificationCode($email, $code)
    {
        try {
            VerificationCode::query()
                ->where('email', $email)
                ->where('code', $code)
                ->where('created_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->where('status', 'unused')
                ->update(['status' => 'used']);

            return true;
        } catch (QueryException | \Exception $e) {
            logger($e);
            return false;
        }
    }
}