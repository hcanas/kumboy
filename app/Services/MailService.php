<?php
namespace App\Services;

use App\Mail\EmailVerificationCode;
use App\Mail\PasswordResetVerificationCode;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendEmailVerificationCode($email, $code)
    {
        try {
            Mail::to($email)->queue(new EmailVerificationCode($code));
            return true;
        } catch (QueryException | \Exception $e) {
            logger($e);
            return false;
        }
    }

    public function sendPasswordResetCode($email, $code)
    {
        try {
            Mail::to($email)->queue(new PasswordResetVerificationCode($code));
            return true;
        } catch (QueryException | \Exception $e) {
            logger($e);
            return false;
        }
    }

}